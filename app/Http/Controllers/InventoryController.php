<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\CommonFunction;
use DB;
use Config;
//Models
use App\Models\FGInventory;
use App\Models\InventoryVault;
use App\Models\ActiveInventory;
use App\Models\InvoiceItemAP;
use App\Models\Promo;
use App\Models\Harvest;
use App\Models\Producttype;
use App\Models\Strainame;
use App\Models\InventoryIgnored;
use App\Models\InventoryVSIgnored;
use App\Models\UPController;
use App\Models\Counter;
use App\Models\InvImportHistory;
use App\Models\InvImportHistoryItem;
class InventoryController extends Controller
{
    use CommonFunction;
    //
    public $invTypes = ['','INV 2','INV 1'];
    public function __construct()
    {

    }

    public function combinePanel()
    {
        $data = [];
        $data['s_date'] = date('Y-m-d', strtotime('today - 31 days'));
        $data['e_date'] = Date('Y-m-d');
        $p_types = Producttype::all();
        return view('inventory.combine_panel',['data' => $data,'p_types' => $p_types]);
    }

    public function getCombines(Request $request)
    {
        $inventory = [];
        $items = ActiveInventory::whereRaw('DATE(harvested_date) >= ?', [$request->s_date])
                ->whereRaw('DATE(harvested_date) <= ?', [$request->e_date])
                ->orderBy('harvested_date')
                ->get()->take(15);
        $cnt = 1;
        foreach($items as $key => $value)
        {
            $value->no         = $cnt;
            $value->i_type     = $value->type;
            $value->h_batch    = $value->Harvest != null?$value->Harvest->harvest_batch_id:'No Harvest';
            $value->strain_lbl = $value->Strain != null?$value->Strain->strain:'No Strain';
            $value->p_type_lbl = $value->AssetType != null?$value->AssetType->producttype:'No Type';
            $value->unit_lbl   = $value->UnitLabel;
            $value->upc_lbl    = $value->UpcLabel;
            $value->coa_lbl    = $value->CoaName;

            $inventory[] = $value;
            $cnt ++;
        }
        return $inventory;
    }

    public function combineItems(Request $request)
    {
        /**
         * Create New Harvest
         * Harvest Batch ID:Date-Strainalis-combined
         */
        return DB::transaction(function () use ($request){
            $strain = Strainame::find($request->data[0]['strainname']);
            $harvest = new Harvest;
            $harvest->harvest_batch_id = Date('Y-m-d').'-'.$strain->strainalias.'-combined';
            $harvest->archived = 1;
            $harvest->save();
            $combined = new FgInventory;
            $combined->parent_id = $harvest->id;
            $combined->strainname = $strain->itemname_id;
            $combined->asset_type_id = $request->p_type;
            $combined->metrc_tag = $request->metrc;
            $combined->um=4;
            $combined->weight    = 0;
            $combined->qtyonhand = 0;
            $combined->status = 1;
            $combined->harvested_date = date('Y-m-d');
            $combined->bestbefore = date('Y-m-d', strtotime('today - 31 days'));
            $insert_data = [];
            foreach($request->data as $item)
            {
                $combined->weight += $item['weight'];
                $combined->qtyonhand += $item['qty'];
                if($item['i_type'] == 1)
                {
                    $insert_data[] = FgInventory::find($item['fgasset_id'])->toarray();
                    FgInventory::find($item['fgasset_id'])->delete();
                }
                else
                {
                    $insert_data[] = InventoryVault::find($item['fgasset_id'])->toarray();
                    InventoryVault::find($item['fgasset_id'])->delete();
                }
            }
            $combined->save();

            $relation_data = [];
            foreach($insert_data as $item)
            {
                $temp = [];
                $temp['parent'] = $combined->fgasset_id;
                $temp['child'] = InventoryIgnored::insert($item);
                $temp['type']   = 1;
                $relation_data[] = $temp;
            }
            $combined->storeHasMany([
                'CombineLog' => $relation_data
            ]);
            return 1;
        });
    }

    public function splitPanel()
    {
        // $histories = InvImportHistory::whereIn('id',[117,118])->get();
        // foreach($histories as $history)
        // {
        //     $items = $history->rItems;
        //     foreach($items as $item)
        //     {
        //         $inv = FGInventory::find($item->inv_id);
        //         if($inv != null)
        //         {
        //             $oMetrc = substr($inv->metrc_tag,0,20);
        //             $tag = substr($inv->metrc_tag,20,strlen($inv->metrc_tag));
        //             $inv->metrc_tag = $oMetrc.'00'.$tag;
        //             $inv->save();
        //         }
        //     }
        // }
        // exit;
        $data = [];
        $data['s_date'] = date('Y-m-d', strtotime('today - 31 days'));
        $data['e_date'] = Date('Y-m-d');
        $p_types = Producttype::all();
        return view('inventory.split_panel',['data' => $data,'p_types' => $p_types]);
    }

    public function getInventory(Request $request)
    {
        $columns = ['harvest_batch_id','strain','producttype.producttype',
                    'upc','coa','qtyonhand','weight','um','harvested_date'
                   ];
        $bCond = ActiveInventory::whereRaw('DATE(harvested_date) >= ?', [$request->s_date])
                                ->whereRaw('DATE(harvested_date) <= ?', [$request->e_date]);
        $totalData = $bCond->count();
        $limit = $request->input('length');
		$start = $request->input('start');
		$order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $totalFiltered = 0;
        if(empty($request->input('search.value'))){
            $totalFiltered  = $bCond->count();
            $inventory = $bCond->offset($start)->limit($limit)->get();
        }
        else
        {
            $search = $request->input('search.value');
            $cond = $bCond->with(['Harvest','Strain','AssetType'])
                    ->whereHas('Harvest',function($query) use ($search){
                        $query->where('harvest_batch_id','like',"%{$search}%");
                    })
                    ->orWhereHas('Strain',function($query) use ($search){
                        $query->where('strain','like',"%{$search}%");
                    })
                    ->orWhereHas('AssetType',function($query) use ($search){
                        $query->where('producttype','like',"%{$search}%");
                    })
                    ->orWhere('metrc_tag','like',"%{$search}%");
            $totalFiltered  = $cond->count();
            $inventory      = $cond->offset($start)->limit($limit)->get();
        }
        $data = [];
        if($inventory){
			foreach($inventory as $i){
                $nestedData = [];
                $nestedData['hBatch']       = $i->Harvest != null?$i->Harvest->harvest_batch_id:'No Harvest';
                $nestedData['metrc_tag']    = $i->metrc_tag;
                $nestedData['strainname']   = $i->strainname;
                $nestedData['strain']       = $i->Strain->strain;
                $nestedData['pType']        = $i->AssetType->producttype;
                $nestedData['qty']          = $i->qtyonhand;
                $nestedData['weight']       = $i->weight;
                $nestedData['upc']          = $i->UPCLabel;
                $nestedData['coa']          = $i->coa;
                $nestedData['um']           = $i->unitVolume != null?$i->unitVolume->name:'No um';
                $nestedData['hDate']        = $i->harvested_date;
                $nestedData['fgasset_id']   = $i->fgasset_id;
                $nestedData['i_type']       = $i->i_type;
				$data[] = $nestedData;
			}
        }
        $json_data = array(
			"draw"			=> intval($request->input('draw')),
			"recordsTotal"	=> intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"			=> $data
		);

		return response()->json($json_data);
    }

    public function _checkMetrcDuplicate(Request $request)
    {
        return ActiveInventory::where('metrc_tag',$request->metrc)->first()!= null?1:0;
    }
    public function splitItem(Request $request)
    {
        $messages = [
            'required'  => 'The :attribute field is required.',
            'numeric'   => 'The :attribute field is number field',
            'gt'        => 'The :attribute field should be great than 1',
            'min'       => 'The :attribute field\'s length should be great than 5',
            'max'       => 'The :attribute field should be less than 99999',
        ];
        $validatedData = $request->validate([
            'metrc'     => 'required|min:5',
            'count'     => 'required|numeric|gt:0',
            'p_type'    => 'gt:0',
            'weight'    => 'required|gt:0|max:99999',
        ],$messages);
        $inventory = ActiveInventory::where([
                                    ['fgasset_id',$request->fgasset_id],
                                    ['i_type',$request->i_type]])->first();
        $parentId = InventoryIgnored::insert($inventory->toarray());
        $metrc = $request->splitData;
        $pType = $request->p_type;

        $nCutPoint = strlen($request->metrc) - 4;

        $metrcTag = [substr($request->metrc,0,$nCutPoint),substr($request->metrc,$nCutPoint,strlen($request->metrc))];
        
        $data = [];
        $data['bulk_import_data'] = [];
        for($i = 0; $i < $request->count; $i ++)
        {
            $data['bulk_import_data'][] = [
                'metrc'     => $request->metrc.($i + 1),
                'strain'    => $inventory->strainname,
                'p_type'    => $request->p_type,
                'weight'    => $request->weight,
                'harvest'   => $inventory->parent_id,
                'i_type'   => $inventory->i_type,
            ];
        }
        $data['strains'] = Strainame::orderby('strain')->get();
        $data['p_types'] = Producttype::where('onordermenu',1)->orderby('producttype')->get();
        $data['harvests'] = Harvest::where('archived',0)->orderBy('created_at','desc')->get();
        $data['default_strain'] = $inventory->strainname;
        $data['default_p_type'] = $request->p_type;
        $data['default_harvest'] = $request->parent_id;
        $data['i_type'] = $request->i_type;
        $data['fgasset_id'] = $request->fgasset_id;
        return view('inventory.import_bulk_confirm',$data);

    }

    public function importPanel()
    {
        $data = [];
        $data['strains']    = Strainame::orderby('strain')->get();
        $data['p_types']    = Producttype::where('onordermenu',1)->orderby('producttype')->get();
        $data['harvests']   = Harvest::where('archived',0)->orderBy('created_at','desc')->get();
        return view('inventory.import_panel',$data);
    }
    public function importInventory(Request $request)
    {
        $file = $request->file('inventoryFile');
        if($file != null)
        {
            $path = $request->file('inventoryFile')->getRealPath();
            $csvArray = array_map('str_getcsv', file($path));
            $fgData = [];
            $vaultData = [];
            $cnt = 0;
            if(count($csvArray) >= 2)
            {
                for($i = 1; $i < count($csvArray); $i ++)
                {
                    $row = $csvArray[$i];
                    $temp = [];
                    $temp['parent_id'] = $row[0];
                    $temp['stockimage'] = $row[1];
                    $temp['strainname'] = $row[2];
                    $temp['asset_type_id'] = $row[3];
                    $temp['upc_fk'] = $row[4];
                    $temp['metrc_tag'] = $row[5];
                    $temp['batch_fk'] = $row[6];
                    $temp['coa'] = $row[7];
                    $temp['um'] = $row[8];
                    $temp['weight'] = $row[9];
                    $temp['qtyonhand'] = $row[10];
                    $temp['status'] = 14;
                    $temp['bestbefore'] = $row[12];
                    $temp['harvested_date'] = $row[13];
                    $temp['datelastmodified'] = date('Y-m-d H:i:s');
                    $temp['created_at'] = date('Y-m-d H:i:s');
                    $temp['updated_at'] = date('Y-m-d H:i:s');
                    if($row[14] == 1)
                    {
                        $model = FGInventory::updateOrInsert(
                            ['metrc_tag' => $temp['metrc_tag']],
                            $temp
                        );
                        $cnt ++;
                    }
                    if($row[14] == 2)
                    {
                        $model = InventoryVault::updateOrInsert(
                            ['metrc_tag' => $temp['metrc_tag']],
                            $temp
                        );
                        $cnt ++;
                    }
                }
            }
            return redirect('inventory/import')->with('success',$cnt.'Inventory is imported successfully!');
        }
        else
        {
            return  redirect('inventory/import')->with('warning','No Selected Files');
        }
    }

    public function bulk_import_confirm(Request $request)
    {
        $messages = [
            'required'  => 'The :attribute field is required.',
            'numeric'   => 'The :attribute field is number field',
            'gt'        => 'The :attribute field should be great than 1',
            'min'       => 'The :attribute field\'s length should be great than 5',
            'max'       => 'The :attribute field should be less than 99999',
        ];
        $validatedData = $request->validate([
            'metrc'     => 'required|min:5',
            'count'     => 'required|numeric|gt:0',
            'i_type'    => 'gt:0',
            'strain'    => 'gt:0',
            'p_type'    => 'gt:0',
            'weight'    => 'required|gt:0|max:99999',
            'harvest'   => 'gt:0',
        ],$messages);
        $nCutPoint = strlen($request->metrc) - 4;

        $metrcTag = [substr($request->metrc,0,$nCutPoint),substr($request->metrc,$nCutPoint,strlen($request->metrc))];
        $data = [];
        $data['bulk_import_data'] = [];
        for($i = 0; $i < $request->count; $i ++)
        {
            $data['bulk_import_data'][] = [
                'metrc'     => $metrcTag[0].(string)((int)$metrcTag[1] + $i),
                'strain'    => $request->strain,
                'p_type'    => $request->p_type,
                'weight'    => $request->weight,
                'harvest'   => $request->harvest,
                'i_type'   => $request->i_type,
            ];
        }
        $data['strains'] = Strainame::orderby('strain')->get();
        $data['p_types'] = Producttype::where('onordermenu',1)->orderby('producttype')->get();
        $data['harvests'] = Harvest::where('archived',0)->orderBy('created_at','desc')->get();
        $data['default_strain'] = $request->strain;
        $data['default_p_type'] = $request->p_type;
        $data['default_harvest'] = $request->harvest;
        //print_r($data['bulk_import_data']);exit;
        return view('inventory.import_bulk_confirm',$data);
    }

    public function bulk_import(Request $request)
    {
        $result = DB::transaction(function () use ($request){
            $default_upc = UPController::where([
                ['strain' , $request->default_strain],
                ['type' , $request->default_p_type],
            ])->first();
            $default_harvest = Harvest::find($request->default_harvest);
            $insert_data = [];
            $histories = [];
            foreach($request->items as $item)
            {
                $temp = $item;
                unset($temp['i_type']);
                //set upc_fk
                $upc = $default_upc;
                
                if($item['strainname'] != $request->default_strain || $item['asset_type_id'] != $request->default_p_type)
                {
                    $upc = UPController::where(
                        [
                            ['strain' , $item['strainname']],
                            ['type' , $item['asset_type_id']],
                        ]
                    )->first();
                }

                $temp['upc_fk'] = $upc != null?$upc->iteminv_id:0;
                //
                $temp['um'] = 4;
                $temp['qtyonhand'] = 1;
                $temp['status'] = 14;
                //set harvest_id
                $harvest = $default_harvest;
                if($item['parent_id'] != $request->default_harvest)
                {
                    $harvest = Harvest::find($item['parent_id']);
                }
                $temp['harvested_date'] = $harvest != null?date('Y-m-d',strtotime($harvest->created_at)):date('Y-m-d');
                $temp['datelastmodified'] = date('Y-m-d H:i:s');
                $temp['created_at'] = date('Y-m-d H:i:s');
                $temp['updated_at'] = date('Y-m-d H:i:s');
                
                if($item['i_type'] == '1')
                {
                    $model = FGInventory::updateOrInsert(
                        ['metrc_tag' => $temp['metrc_tag']],
                        $temp
                    );
                }
                else
                {
                    $model = InventoryVault::updateOrInsert(
                        ['metrc_tag' => $temp['metrc_tag']],
                        $temp
                    );
                }
                
                //generate history data
                $model = $model->first();
                $hItem = [];
                $tmpHistory = [];
                $tmpHistory['h_id'] = $model->parent_id;
                $tmpHistory['strain_id'] = $request->default_strain;
                $tmpHistory['category_id'] = $request->default_p_type;
                $tmpHistory['importer'] = auth()->user()->id;
                $tmpHistory['imported_at'] = date('Y-m-d H:i:s');
                
                $hItem['inv_id'] = $model->fgasset_id;
                $hItem['i_type'] = $model->type;
                $tmpHistory['items'] = [$hItem];
                
                $b_exist = false;
                
                foreach($histories as $key => $history)
                {
                    if($history['h_id'] == $model->parent_id)
                    {
                        $histories[$key]['items'][] = $hItem;
                        $b_exist = true;
                        break;
                    }
                }

                if(!$b_exist)
                {
                    $histories[] = $tmpHistory;
                }
            }
            foreach($histories as $history)
            {
                $history['fgasset_id'] = $request->fgasset_id;
                $history['i_type'] = $request->i_type;
                //InvImportHistory,InvImportHistoryItem
                $result = new InvImportHistory($history);
                $result->save();
                $result->storeHasMany(['rItems' => $history['items']]);
            }
            return 1;
        });

        return redirect('inventory/import')->with('success',count($request->items).'Inventory is imported successfully!');
    }

    public function archive_imported()
    {
        $histories = InvImportHistory::where('status', 0)->get();
        $data = [];
        foreach($histories as $history)
        {
            $harvest = $history->rHarvest;
            $temp = [
                'id' => $history->id,
                'batch_id' => $harvest != null ? $harvest->harvest_batch_id:'Deleted Harvest',
                'strain_label'  => $history->StrainLabel,
                'type'          => $history->CategoryLabel,
                'qty' => $history->rItems()->count(),
                'date' => date('m/d/Y H:i:s', strtotime($history->imported_at)),
                'inventory' => []
            ];
            foreach($history->rItems as $item)
            {
                $inventory = $item->rFgInventory != null ? $item->rFgInventory : $item->rInventoryVault;
                if($inventory != null)
                {
                    $tmp = [];
                    $tmp['metrc_tag'] = $inventory->metrc_tag;
                    $tmp['weight'] = $inventory->weight;
                    $tmp['i_type_label'] = $this->invTypes[$item->i_type];

                    $temp['inventory'][] = $tmp;
                }
            }

            $data[] = $temp;
        }
        
        return view('inventory.archive_imported',['data' => $data,
                                                'start_date' => date('m/d/Y', strtotime('today - 31 days')),
                                                'end_date' => Date('m/d/Y')]);
    }
    public function _approveHistory(Request $request)
    {
        $date_range = $this->convertDateRangeFormat($request->date_range);
        $histories = InvImportHistory::whereRaw('DATE(imported_at) >= ?', [$date_range['start_date']])
                                     ->whereRaw('DATE(imported_at) <= ?', [$date_range['end_date']])
                                     ->where('status',1)->orderBy('approved_at','desc')->get();
        $data = [];
        foreach($histories as $history)
        {
            $temp = [];
            $temp['items'] = [];
            $temp['strainLabel'] = '';
            $temp['pTypeLabel'] = '';
            $harvest = $history->rHarvest;
            if($harvest != null)
            {
                $temp['batch_id'] = $harvest->harvest_batch_id;
                $temp['id'] = $history->id;
                $temp['strainLabel'] = $harvest->Strain != null?$harvest->Strain->strain:'';
                $temp['pTypeLabel'] = $harvest->Strain != null?$harvest->Strain->strain:'';
                $temp['qty'] = $history->rItems()->count();
                $temp['importedDate'] = date('m/d/Y H:i:s', strtotime($history->imported_at));
                $temp['approveDate'] = date('m/d/Y H:i:s', strtotime($history->approved_at));
                $temp['importerName'] = $history->rImporter != null ? $history->rImporter->name : 'Deleted User';
                $temp['approverName'] = $history->rApprover != null ? $history->rApprover->name : 'Deleted User';
                
                foreach($history->rItems as $item)
                {
                    $inventory = $item->rFgInventory != null ? $item->rFgInventory : $item->rInventoryVault;
                    if($inventory != null)
                    {
                        $tmp = [];
                        $tmp['metrc_tag'] = $inventory->metrc_tag;
                        $tmp['weight'] = $inventory->weight;
                        $tmp['i_type_label'] = $this->invTypes[$item->i_type];

                        $temp['items'][] = $tmp;
                    }
                }
            }

            $data[] = $temp;
        }

        return response()->json($data);
    }
    public function _approve_imported(Request $request)
    {
        $history = InvImportHistory::find($request->id);
        $history->approver = auth()->user()->id;
        $history->approved_at = date('Y-m-d H:i:s');
        $history->status = 1;
        $history->save();
        FGInventory::where([
            ['parent_id',$history->h_id],
            ['status',14],
        ])->update(['status' => 1]);
        InventoryVault::where([
            ['parent_id',$history->h_id],
            ['status',14],
        ])->update(['status' => 1]);
        
        $inventory = FGInventory::find($history->fgasset_id);
        if($inventory == null)
        {
            $inventory = InventoryVault::find($history->fgasset_id);
        }
        if($inventory != null)
        {
            $inventory->status = 23;
            $inventory->save();
        }
        return response()->json(['success' => $history->id]);
    }

    public function getInventoryStatus(Request $request)
    {
        $aInvList = [];
        $fg = ActiveInventory::select(DB::raw('strainname'),DB::raw('asset_type_id'),DB::raw('upc_fk'),
              DB::raw('count(*) as qty'),DB::raw('sum(weight) as weights'))
              ->with(['Strain','AssetType'])
              ->where('qtyonhand','>',0)
              ->whereHas('AssetType',function($query) {
                    $query->where('onordermenu',1);
                })
              ->groupby('strainname','asset_type_id')
              ->orderby('strainname','asc')
              ->orderby('asset_type_id','asc')
              ->get();
        foreach($fg as $i)
        {
            $alreay_requested = InvoiceItemAP::whereHas('Order', function($q) {
                $q->whereIn('status', [0,1,2]);
            })->where([
                    ['strain',$i->strainname],
                    ['p_type',$i->asset_type_id],
                    ['invoice_id','!=',$request->id]
            ])->get();
            $temp = [];
            $temp['strain'] = $i->strain->strain;
            $temp['p_type'] = $i->AssetType->producttype;
            $temp['strain_id'] = $i->strainname;
            $temp['p_type_id'] = $i->asset_type_id;
            $temp['qty']    = $i->qty - $alreay_requested->sum('qty');
            
            $upc = UPController::where(
                [
                    ['strain',$i->strainname],
                    ['type',$i->asset_type_id]
                ])->first();
            $weight = $upc != null?$upc->weight:0;
            $bp = $upc != null?$upc->baseprice:0;
            $taxE = $upc != null?$upc->taxexempt:0;
            $temp['weight'] = number_format((float)($i->weights - $alreay_requested->sum('qty') * $weight), 2, '.', '');
            $temp['bp'] = number_format((float)($bp), 2, '.', '');
            $temp['taxE'] = $taxE;
            if($temp['qty'] >= 1)
                $aInvList[] = $temp;
        }
        $data = [];
        $data['inventoryStatus'] = $aInvList;
        $data['soNumber'] = Counter::where('key','invoice')->first()->prefix.Counter::where('key','invoice')->first()->value;
        $data['discounts'] = Promo::all();
        return response()->json($data);
    }
}
