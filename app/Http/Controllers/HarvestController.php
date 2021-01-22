<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Models\Harvest;
use App\Models\HarvestItem;
use App\Models\HarvestDry;
use App\Models\HarvestItemDry;
use App\Models\HarvestHistory;
use App\Models\Strainame;
use App\Models\Cultivator;
use App\Models\Unit;
use App\Models\LocationArea;
use App\Models\Producttype;
use App\Models\UPController;
use App\Models\HoldingInventory;
use App\Models\HarvestDynamics;
use App\Models\Curning;
use App\Models\FGInventory;
use App\Models\WaistType;
use App\Models\HarvestFreshItem;
use App\Models\HarvestCurningAsset;
use App\Models\BatchRoom;
use App\Models\Invupccont;
use App\Models\HarvestRoom;
use App\Models\HarvestWaistItem;
use App\User;
use JavaScript;
use DB;
use Picqer;

class HarvestController extends Controller
{
    private $default_date_range = "2019-01-01";
    private $default_container_weight = 453.59;
    private $default_havest_info = [
        'cultivator_company_id' => 85001,
        'strain_id'             => 1,
        'cultivator_license_id' => 1
    ];
    //
    private $default_plant_tag = '1A4060200001965000009319';
    //
    public function __construct()
    {
        
    }
    //create,edit
    public function create(Request $request)
    {
        $harvest_id = $request->id;
        
        //prepare data;
        $data['strain_list']     = Strainame::all();
        $data['company_list']    = LocationArea::all();
        $data['cultivator_list'] = Cultivator::all();
        $data['unit_list']       = Unit::all();
        $data['mode']            = $harvest_id == null?'create':'edit';
        $data['harvest']         = new Harvest;
        
        if($harvest_id != null)
        {
            $data["harvest"] = Harvest::find($harvest_id);
            $data["harvest"]['items'] = $data["harvest"]->items()->get();
        }
    
        JavaScript::put([
            'harvest_items' => $data["harvest"]['items'],
            'mode' => $data['mode'],
            'harvest_id' => $harvest_id
        ]);
        
        return view('harvest/form',$data)->with('success','New Harvest updated Successfully');
    }

    public function store(Request $request)
    {
        $harvest = DB::transaction(function () use ($request){
            
            $now_harvest = Harvest::find($request->harvest_id);
            
            //there is no changed data
            if($now_harvest->cultivator_company_id == $request->cultivator_company_id && $now_harvest->strain_id == $request->strain_id && $now_harvest->cultivator_license_id == $request->cultivator_license_id)
            {
                return ["code" => '0'];
            }
            //to check the default harvest
            $new_havest_info = [];
            $new_havest_info['cultivator_company_id'] = $request->cultivator_company_id;
            $new_havest_info['strain_id']             = $request->strain_id;
            $new_havest_info['cultivator_license_id'] = $request->cultivator_license_id;
            
            //if default true not default false
            $new_kind = $this->is_default_harvest($new_havest_info);

            $strain = Strainame::find($request->strain_id)->strainalias;
            //retrive the harvest_batch_id
            $new_havest_info['harvest_batch_id'] = $this->create_harvest_batch_id(substr($now_harvest->created_at,0,10),$strain);
            if($new_kind)
            {
                $now_harvest->update($new_havest_info);
                return ["code" => '1'];
            }
            else
            {
                $exist_harvest_id = $this->is_exist_harvest($new_havest_info['harvest_batch_id'],$new_havest_info['cultivator_company_id']);
                
                if($exist_harvest_id != -1)
                {
                    if($request->merge == false)
                    {
                        //require the merge confirm
                        return ["code" => '2',
                                "harvest_batch_id" => $new_havest_info['harvest_batch_id']];
                    }
                    //user allow the merge
                    else
                    {
                        //merge code here
                        $exist_harvest = Harvest::find($exist_harvest_id);
                        $exist_harvest->update($new_havest_info);
                        $items = Harvest::find($request->harvest_id)->items()->update(['harvest_id' => $exist_harvest_id]);
                        
                        $exist_harvest->total_weight += Harvest::find($request->harvest_id)->total_weight;
                        $exist_harvest->save();
                        Harvest::find($request->harvest_id)->delete();
                        return ["code" => '1','harvest_id' => $exist_harvest_id];
                    }
                }
                else
                {
                    $now_harvest->update($new_havest_info);
                    return ["code" => '1'];
                }
            }

            return ["code" => '999'];
        });

        return json_encode($harvest);
    }

    public function list(Request $request)
    {
        $waist_type_list = WaistType::all();
        foreach($waist_type_list as $item)
        {
            $waist_ids[] = $item->id;
        }
        JavaScript::put([
            'start_date' => date('m-d-Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
            'perm' => $request->perm,
            'waist_ids' => $waist_ids
        ]);

        return view('harvest/list',['perm' => $request->perm,'waist_type_list' => $waist_type_list]);
        
    }

    public function _deduct_waist(Request $request)
    {
        $harvest = DB::transaction(function () use ($request){
            $harvest = Harvest::find($request->id);
            $items = $request->items;

            $total_weight = 0;
            foreach($items as $item)
            {
                $total_weight += $item['weight'];
            }

            $harvest->total_weight -= $total_weight;
            $harvest->save();
            $harvest->storeHasMany(['waist_item' => $request->items]);
            return $harvest;
        });

        return $harvest->id;
    }

    public function send_dynamcis(Request $request)
    {
        $id = $request->id;
        $harvest = Harvest::find($id);
        $harvestDynamics = new HarvestDynamics;
        $harvestDynamics->parent_id = $harvest->id;
        $harvestDynamics->unit_weight = $harvest->unit_weight;
        $harvestDynamics->cultivator_company_id = $harvest->cultivator_company_id;
        $harvestDynamics->total_weight = $harvest->total_weight;
        $harvestDynamics->strain_id = $harvest->strain_id;
        $harvestDynamics->cultivator_license_id = $harvest->cultivator_license_id;
        $harvestDynamics->save();

        $history = new HarvestHistory;
        $history->harvest_id = $id;
        $history->dynamics = date('Y-m-d');
        $history->save();

        $harvest->archived = 1;
        $harvest->save();

        return 1;
    }

    public function list_dry(Request $request)
    {
        $producttypes = Producttype::all();
        JavaScript::put([
            'start_date' => date('m-d-Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
            'perm' => $request->perm,
        ]);

        return view('harvest/list_dry',['producttypes' => $producttypes,]);
    }
    
//---------------------------fresh---------------------------------------
    public function form_fresh(Request $request)
    {
        $item_type = "Fresh Frozen";

        $id = $request->id;
        $data['harvest'] = Harvest::with(['Strain','License','UnitOfWeight'])->find($id);
        
        //js_data
        $js_data['tare'] = Producttype::where('producttype',$item_type)->first()->tare;
        $js_data['total_weight'] = $data['harvest']->total_weight;
        $js_data['harvest_id'] = $data['harvest']->id;
        $js_data['item_type'] = $item_type;
        $js_data['batch_id'] = $data['harvest']->harvest_batch_id;
        JavaScript::put($js_data);

        return view('harvest/form_fresh',$data);
    }

    public function store_fresh(Request $request)
    {
        $flag = 0;
        $flag = DB::transaction(function () use ($request){

            $harvest = Harvest::find($request->id);
            //insert curning asset
            $harvest->storeHasMany(['fresh_item' => $request->items]);
            $weight = 0;
            foreach($request->items as $item)
            {
                $weight += $item['weight'];
            }

            $harvest->total_weight -= $weight;
            //create Holding Inventory
            $holdingInventory = new HoldingInventory;
            $upc = 0;
            
            $holdingInventory->strainname    = $harvest->strain_id;
            $holdingInventory->asset_type_id = 6;
            $holdingInventory->upc_fk        = null;
            $holdingInventory->batch_fk      = $harvest->harvest_batch_id;
            $holdingInventory->qtyonhand     = count($request->items);
            $holdingInventory->weight        = $weight;
            $holdingInventory->um            = $harvest->unit_weight;
            $holdingInventory->parent_id     = $request->id;

            $holdingInventory->save();
            //log History if this is archived
            if($harvest->total_weight == 0)
            {
                $harvest->archived=1;
                
                //save history
                $history = HarvestHistory::where('harvest_id',$harvest->get_harvestsId1())->first();
                $history->dynamics = '0000-00-00';
                $history->dry = '0000-00-00';
                $history->curning = '0000-00-00';
                $history->holding = date('Y-m-d');
                $history->save();
            }
            //save Harvest
            $harvest->save();
            return 1;
        });

        return $flag;
    }
//---------------------------/fresh--------------------------------------

    public function get_harvest_dry_table_data(Request $request)
    {
        $harvest = new HarvestDry;
        return json_encode($harvest->get_harvest_dry_table_data($request->date_range));
    }

    /*
    $harvest_id = $request->id;

            $data['mode'] = $request->mode;
            $data['harvest'] = HarvestDry::with(['Strain','License','UnitOfWeight'])->find($harvest_id);
            if($data['harvest']->archived == '1')
            {
                return view('harvest/list_dry')->with('This Harvest has already been reweighted','info');
            }
            
            if($data['harvest'] == null)
            {
                return "Error No found page you are looking for";
            }

            JavaScript::put([
                'id'=>$data['harvest']->id,
                'total_weight' => $data['harvest']->total_weight,
            ]);

            return view('harvest/form_dry',$data);
    */
    public function form_dry(Request $request)
    {
        $id = $request->id;

        $data['harvest'] = HarvestDry::with(['Strain','License','UnitOfWeight'])->find($id);
        $aa = HarvestDry::find($id);
       
        //original item count
        $data['originalItemCount'] = count($data['harvest']->getHarvest()->items()->get());
        //original total wegiht
        $data['originalTotalWeight'] = $data['harvest']->getHarvest()->total_weight;
        //original batch id
        $data['parentHarvestBatchId'] = $data['harvest']->getHarvest()->harvest_batch_id;
        
        if($data['harvest']->archived == '1')
        {
            return view('harvest/list_dry')->with('This Harvest has already been reweighted','info');
        }

        if($data['harvest'] == null)
        {
            return "Error No found page you are looking for";
        }

        JavaScript::put([
            'id'=>$data['harvest']->id,
            'item_count' => $data['originalItemCount'],
            'total_weight' => $data['originalTotalWeight']
        ]);

        return view('harvest/form_dry',$data);
    }

    public function store_dry(Request $request)
    {
        $harvestDry = DB::transaction(function () use ($request){
            //$harvest = Harvest::find($id);
            //$harvest_data = $harvest->toarray();
            //$harvest_data['total_weight'] = 0;
            
            $id = $request->id;
            $harvestDry = HarvestDry::find($id);
            //store items
            $harvestDry->storeHasMany(['items' => $request->items]);
            $harvestDry->archived = 1;
            $harvestDry->save();

            //calc the total dry weight
            $total_weight = 0;
            foreach($request->items as $item)
                $total_weight += $item['weight'];
            //send to curing
            $curning = new Curning;
            
            $curning->parent_id     = $harvestDry->id;
            $curning->total_weight  = $total_weight;
            $curning->remain_weight = $total_weight;
            $curning->unit_weight   = $harvestDry->unit_weight;
            $curning->strain_id     = $harvestDry->strain_id;
            $curning->cultivator_company_id = $harvestDry->cultivator_company_id;
            $curning->cultivator_license_id = $harvestDry->cultivator_license_id;
            $curning->save();
            
            //save history
            $history = HarvestHistory::where('harvest_id',$curning->get_harvestsId1())->first();
            $history->curning = date('Y-m-d');
            $history->save();

            return $harvestDry;
        });

        return $harvestDry->id;
    }
    
        public function _list_harvest_barcode(Request $request)
    {
        $harvest = Harvest::find($request->id);
        $harvest_batch_id = $harvest->harvest_batch_id;
        $item_count = Count($harvest->items()->get());
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode_html = '<div style="margin-bottom:15px"><img src="data:image/png;base64,' . base64_encode($generator->getBarcode($harvest_batch_id, $generator::TYPE_CODE_128)) . '">';
        $barcode_html .= '<br>';
        $barcode_html .= '<span style="font-size:8px">'.$harvest_batch_id.' | Total Plants = '.$item_count.'</span></div>';
        $barcode_html .= '<div><img src="data:image/png;base64,' . base64_encode($generator->getBarcode($request->count, $generator::TYPE_CODE_128)) . '">';
        $barcode_html .= '<br>';
        $barcode_html .= '<span style="font-size:8px">'.$request->count.'</span></div>';

        return $barcode_html;
    }
//-------------------------Curning Stage---------------------------------------
    public function curning(Request $request)
    {
        $producttypes = Producttype::all();
        JavaScript::put([
            'start_date' => date('m-d-Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
            'perm' => $request->perm,
        ]);

        return view('harvest/curning',['producttypes' => $producttypes,]);
    }

    public function get_curning_table_data(Request $request)
    {
        $curning = new Curning;
        return json_encode($curning->get_list($request->date_range));
    }

    public function _curning_harvest_barcode(Request $request)
    {
      //  $harvest_dry_batch_id = "DRY".Curning::find($request->id)->get_harvestsId();
        $harvest_dry_batch_id = Curning::find($request->id)->get_harvestsId();
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode_html = '<div ><img src="data:image/png;base64,' . base64_encode($generator->getBarcode($harvest_dry_batch_id, $generator::TYPE_CODE_128)) . '">';
        $barcode_html .= '<br>';
        $barcode_html .= '<span>'.$harvest_dry_batch_id.'</span></div>';

        return $barcode_html;
    }
    
    public function form_curning_asset(Request $request)
    {
        $id = $request->id;
        /**
         * datas for blade build
         */
        //Batch Info
        $blade_data['curning'] = Curning::find($id);
        $blade_data['batchId'] = $blade_data['curning']->get_harvestsId();
        //product types
        $blade_data['productype'] = Producttype::all();   
        /*
            datas for javascript
        */
        //tares
        $js_data['tares'] = [];
        foreach($blade_data['productype'] as $item)
        {
            $js_data['tares'][$item->producttype_id] = $item->tare;
        }
        //curing id
        $js_data['curning_id'] = $blade_data['curning']->id;
        //total_weight
        $js_data['total_weight'] = $blade_data['curning']->remain_weight;
        
        // //$data['containerCount'] = floor($data['curning']->total_weight/$this->default_container_weight) + 1;
        // $total_weight = $data['curning']->total_weight;
        // $data['containerCount'] = 0;
        // while($total_weight >= $this->default_container_weight)
        // {
        //     $data['containerCount'] ++;
        //     $data['containerWeight'][] = $this->default_container_weight;
        //     $total_weight -= $this->default_container_weight;
        // }
        
        // $data['containerCount'] ++;
        // $data['containerWeight'][] = $total_weight;

        JavaScript::put($js_data);

        return view('harvest.form_curning_asset',$blade_data);
    }

    public function store_curning_asset(Request $request)
    {
        $flag = 0;
        $flag = DB::transaction(function () use ($request){

            $curning = Curning::find($request->id);
            //insert holding inventory
            $inventory_item = [];
            $batch_id = $curning->get_harvestsId();

            $sub_total_weight = 0;

            foreach($request->items as $item)
            {
                $tmp = [];
                $tmp['strainname']    = $curning->strain_id;
                $tmp['asset_type_id'] = $item['type'];
                $tmp['metrc_tag'] = $item['metrc'];
                $tmp['batch_fk']      = $batch_id;
                $tmp['weight']        = $item['weight'];
                $tmp['um']            = $curning->unit_weight;
                $tmp['parent_id']     = $curning->get_harvestsId1();
                $tmp['qtyonhand']     = 1;
                $inventory_item[] = $tmp;

                $sub_total_weight += $item['weight'];
            }
            HoldingInventory::insert($inventory_item);
            
            //Store Curning
            
            //remove previous curing assets items
            //$curning->asset()->delete();
            //remove previous inventory item
            //HoldingInventory::where('parent_id',$curning->id)->delete();
            //insert curning asset
            $curning->storeHasMany(['asset' => $request->items]);
            $curning->remain_weight -= $sub_total_weight;
            //check remain weight
            if($curning->remain_weight == 0)
                $curning->archived = 1;
            $curning->save();
            return $inventory_item;
        });

        $request->session()->flash('alert-success', 'One Harvest processed successfully!');
        return $flag;
    }

    public function _curning_to_holding(Request $request)
    {
        $holdingInventory = new HoldingInventory;
        $upc = 0;
        $curning = Curning::find($request->id);
        
        $holdingInventory->strainname    = $curning->strain_id;
        $holdingInventory->asset_type_id = $request->type_id;
        $holdingInventory->upc_fk        = null;
        $holdingInventory->batch_fk      = $curning->get_harvestsId();
        $holdingInventory->qtyonhand     = $request->qty;
        $holdingInventory->weight        = $request->w;
        $holdingInventory->um            = $curning->unit_weight;
        $holdingInventory->parent_id     = $request->id;

        $holdingInventory->save();

        $curning->remain_weight -= $request->w;
        if($curning->remain_weight <= 0)
        {
            $curning->archived = 1;

            //log to harvest History
            $harvest_id = $curning->get_harvestsId1();
            $history = HarvestHistory::where('harvest_id',$harvest_id)->first();
            $history->holding = date('Y-m-d');
            $history->save();
        }
        
        $curning->save();

        return '1';
    }

public function _get_curning_barcode(Request $request)
{
$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
$barcode_html = "";
$text_data = $request->all();
$cnt = 0;
$style = 'margin-bottom:15px;';
foreach($text_data as $text)
{
if($cnt == 3)
//$style .= 'margin-left:150px;';
$style .= 'margin-left:0px;';
//batchid
$barcode_html .= '<div style='.$style.'><img src="data:image/png;base64,' . base64_encode(
$generator->getBarcode($text, $generator::TYPE_CODE_128)) . '">';
$barcode_html .= '<br>';
$barcode_html .= '<span style="font-size:8px;text-align:center">'.$text.'</span></div>';
$cnt ++;

}

return response()->json($barcode_html);
}

    public function _throw_curing(Request $request)
    {
        $curing_id = $request->id;
        $metrc     = $request->metrc;
        $curning = Curning::find($curing_id);
        $batch_id = $curning->get_harvestsId1();

        $waist = new HarvestWaistItem;
        $waist->parent_id  = $batch_id;
        $waist->waist_type = 5;
        $waist->weight     = $curning->remain_weight;
        $waist->metrc      = $metrc;

        $waist->save();
        $curning->remain_weight = 0;
        $curning->archived = 1;
        $curning->save();
        return '1';
    }

    public function process_history()
    {
        JavaScript::put([
            'start_date' => date('m-d-Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);
        
        return view('harvest/process_history');
    }

    public function get_process_history_data(Request $request)
    {
        $curning = new Curning;
        return json_encode($curning->get_process_history_data($request->date_range));
    }
//-------------------------./Curning Stage---------------------------------------
//------------------------------------history-------------------------------
    public function history()
    {
        JavaScript::put([
            'start_date' => date('m-d-Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);

        return view('harvest/history');
    }

    public function get_history_data(Request $request)
    {
        $harvestHistory = new HarvestHistory;
        
        return json_encode($harvestHistory->get_history($request->date_range,$request->mode));
    }
//------------------------------------/history------------------------------

//-------------------------------------dashboard---------------------------------
    public function dashboard(Request $request)
    {
        //$request->batch_id = '2019-11-04_ORG';

        $harvest = Harvest::where('harvest_batch_id',$request->batch_id)->first();
        $data = [];
        
        if($request->batch_id == null)
        {
            $data = 'first';
        }
        elseif($harvest == null)
        {
            $data = null;
        }
        else
        {
            $data['harvest'] = $harvest;
            $data['harvest_items'] = $harvest->items()->get();
            $data['dynamics'] = HarvestDynamics::where('parent_id',$harvest->id)->first();

            if($data['dynamics'] != null)
            {
                $data['dry']      = HarvestDry::where('parent_id',$data['dynamics']['id'])->first();
                if($data['dry'] != null)
                {
                    $data['curning']  = Curning::with(['asset'])->where('parent_id',$data['dry']->id)->get();
                    $data['holding'] = [];
                    $data['fg'] = [];
                    foreach($data['curning'] as $item)
                    {
                        foreach(HoldingInventory::where('parent_id',$item->id)->orderby('created_at')->get() as $val)
                        {
                            $data['holding'][] = $val;
                        }

                        foreach(FGInventory::where('parent_id',$item->id)->orderby('harvested_date')->get() as $val)
                        {
                            $data['fg'][] = $val;
                        }
                        
                    }

                    $data['invoice_items'] = Curning::get_invoice_items(array_column($data['curning']->toarray(),'id'));
                }
                else
                {
                    $data['curning']  = null;
                    $data['holding'] = [];
                    $data['fg'] = [];
                    $data['invoice_items']  = [];
                }
            }
            else
            {
                $data['dry'] = null;
                $data['curning']  = null;
                $data['holding'] = [];
                $data['fg'] = [];
                $data['invoice_items']  = [];
            }
            
        }   
        return view('harvest.dashboard',['data' => $data,'batch_id'=>$request->batch_id]);
    }
//-------------------------------------/dashboard---------------------------------

//-------------------------------------.dashboard---------------------------------
    public function form_transfer()
    {
        $data['rooms'] = LocationArea::all();
        return view('harvest.form_transfer',$data);
    }

    public function get_item_from_barcode(Request $request)
    {
        //will display on the page
        $status = ['On Harvest','On Dynamics','On Dry','On Curing','Archived'];
        $type_name = ["Harvest Batch","Holding Inventory Item","Fresh Item","Curing Item"];
        //scanned barcode
        $barcode = $request->barcode;
        //result structure
        $result = ['success' => '1','item' => []];
        
        //Search About Harvest
        $harvest = Harvest::where('harvest_batch_id',$barcode)->first();
        if($harvest != null)
        {
            $result['item'] = $harvest;
            $result['status'] = $status[$this->check_harvest_status($harvest->id)];
            $result['type'] = 0;
            $result['weight'] = $harvest->remain_weight;
            $result['id'] = $harvest->id;

            if($result['status'] == 'Archived')
            {
                $result['success'] = 0;
                return response()->json($result);
            }
            //get current location
            $location = $harvest->batch_location()->with('location')->where('status','0')->first();
            $result['current_location'] = $location != null?$location->location->name:'No Current Location';
        }
        else
        {
            //Search on Inventory Hold
            $inventory = HoldingInventory::where('metrc_tag',$barcode)->first();
            if($inventory == null)
            {
                //$barcode = '644824709871';
                $upc = Invupccont::where('upc',$barcode)->first();
                if($upc != null)
                {
                    $inventory = $upc->HoldingInventory;
                }
                else
                {
                    $inventory = null;
                }
            }
            if($inventory != null)
            {
                $result['id'] = $inventory->id;
                $result['item']  = $inventory;
                $result['type'] = 1;
                $result['status'] = null;
                $result['weight'] = $inventory->weight;
                //get current Location
                $location = $inventory->batch_location()->with('location')->where('status','0')->first();
                $result['current_location'] = $location != null?$location->location->name:'No Current Location';
            }
            else
            {
                //Search on Fresh Item
                $fresh_item = HarvestFreshItem::where('metrc',$barcode)->first();
                if($fresh_item != null)
                {
                    $result['id'] = $fresh_item->id;
                    $result['item']  = $fresh_item;
                    $result['type'] = 2;
                    $result['status'] = null;
                    $result['weight'] = $fresh_item->weight;
                    
                    $location_log = BatchRoom::where('barcode',$barcode)->where('status',0)->first();
                    $result['current_location'] = $location_log != null?LocationArea::find($location_log->room_id)->name:"No Location";
                }
                else
                {
                    //Search on Holding Asset
                    $curing_item = HarvestCurningAsset::where('metrc',$barcode)->first();
                    if($curing_item != null)
                    {
                        //curing item
                        $result['id'] = $curing_item->id;
                        $result['item']  = $curing_item;
                        $result['type'] = 3;
                        $result['status'] = null;
                        $result['weight'] = $curing_item->weight;
                        $location_log = BatchRoom::where('barcode',$barcode)->where('status',0)->first();
                        $result['current_location'] = $location_log != null?LocationArea::find($location_log->room_id)->name:"No Location";
                    }
                    else
                    {
                        $result['success'] = 0;
                    }
                }
            }
        }

        //if result is not null
        if($result['success'] != 0)
        {
            $result['type_name'] = $type_name[$result['type']];
        }
        
        return response()->json($result);
    }

    public function check_harvest_status($harvest_id)
    {
        $dynamic = HarvestDynamics::where('parent_id',$harvest_id)->first();
        //on the harvest
        if($dynamic == null)
        {
            return 0;
        }

        $dry = HarvestDry::where('parent_id',$dynamic->id)->first();
        //on the dynamic
        if($dry == null)
        {
            return 1;
        }
        
        $curing = Curning::where('parent_id',$dry->id)->first();
        //on the dry
        if($curing == null)
        {
            return 2;
        }
        //on curing
        if($curing->archived == 0)
        {
            return 3;
        }
        //on the curing stage
        else
        {
            return 4;
        }
    }

    public function store_transfer(Request $request)
    {
        $insert_data = [];
        $temp = [];
        foreach($request->items as $item)
        {
            //search the previous room which status is 0 that means it is active now
            $prev_location = BatchRoom::where('barcode',$item['barcode'])
                                      ->where('status',0)
                                      ->first();
            if($prev_location != null)
            {
                $prev_location->status = 1;
                $prev_location->save();
                $temp['parent_id'] = $prev_location->id;
            }
            else
            {
                $temp['parent_id'] = -1;
            }

            $temp['barcode'] = $item['barcode'];
            $temp['record_id'] = $item['record_id'];
            $temp['type'] = $item['type'];
            $temp['room_id'] = $item['room_id'];
            $temp['status'] = 0;
            $temp['handler'] = auth()->user()->id;
            $temp['created_at'] = Date('Y-m-d H:i:s');
            $temp['updated_at'] = Date('Y-m-d H:i:s');
            $insert_data[] = $temp;
        }
        BatchRoom::insert($insert_data);
        return $insert_data;
    }

    public function transfer_history()
    {
        // $barcode = '644824709871';
        // $upc = Invupccont::where('upc',$barcode)->first();
        // if($upc != null)
        // {
        //     $holding = $upc->HoldingInventory;
        //     echo $holding->upc_fk;
        // }
        // else
        // {
        //     $inventory = null;
        // }

        

       
        $data['rooms'] = LocationArea::all();
        return view('harvest.transfer_history',$data);
    }

    public function get_transfer_history_table_data(Request $request)
    {
        $type_name = ["Harvest Batch","Holding Inventory Item","Fresh Item","Curing Item"];
        if($request->type == '0')
        {
            $barcode = $request->barcode;
            $items = BatchRoom::where('barcode',$barcode)->with(['location','handle_user'])->orderby('created_at')->get();
            foreach($items as $item)
            {
                $item['type'] = $type_name[$item['type']];
            }
        }
        else
        {
            $rid = $request->room_id;
            $items = BatchRoom::where('room_id',$rid)->where('status',0)->get();
            foreach($items as $item)
            {
                $item['type'] = $type_name[$item['type']];
                if(User::find($item['handler']) != null)
                    $item['handler'] = User::find($item['handler'])->name;
                else
                    $item['handler'] = "No Recorded User";
                
                $item['child_history'] = BatchRoom::where('barcode',$item->barcode)->with(['location','handle_user'])->orderby('created_at')->get();
            }
        }

        return response()->json($items);
    }
//-------------------------------------./dashboard---------------------------------

//-------------------------------------Plant Room Builder---------------------------------
    public function room_builder()
    {
        //get last 30 days from now
        $data['rooms'] = LocationArea::all();
        $data['plant_room'] = HarvestRoom::where('archived',0)->get();
        $js_data['s_date'] = date('m/d/Y', strtotime('today - 31 days'));
        $js_data['e_date'] = date('m/d/Y');
        JavaScript::put($js_data);

        return view('harvest.room_builder',$data);
    }

    public function _get_room_builder_table_data(Request $request)
    {
        $data = HarvestRoom::with(['room_name','user'])->where('archived',0)->get();
        return response()->json($data);
    }

    public function form_room_builder(Request $request)
    {
        $data['id'] = $request->id;
        
        //check new or update
        if($data['id'] == -1)
        {
            $room_id = $request->r_id;
            //blade data
            $data['matrix_col'] = $request->matrix_type;
            $data['room_id'] = $room_id;
            $data['total_cnt'] = 125*$data['matrix_col']*4;
            $data['room_name'] = LocationArea::find($room_id)->name;
            $data['user'] = auth()->user()->name;
            $data['cration_date'] = date('Y-m-d');

            for($i = 0; $i < $data['total_cnt']; $i ++)
            {
                $model[] = '';
            }
            $data['tables'][] = $model;
            $data['tables'][] = $model;
            $data['tables'][] = $model;
        }
        else
        {
            $sel_room = HarvestRoom::with(['room_name','user'])->find($data['id']);
            $data['matrix_col'] = $sel_room->matrix_col;
            $data['room_id'] = $sel_room->room_id;
            $data['total_cnt'] = 125*$sel_room->matrix_col*4;
            $data['room_name'] = $sel_room->room_name->name;
            //$data['user'] = $sel_room->user->name;
            $data['cration_date'] = $sel_room->created_at;
            $data['tables'] = unserialize($sel_room->tables);
        }
        //print_r($data['tables']);exit;
        return view('harvest.form_room_builder',$data);
    }

    public function store_room_builder(Request $request)
    {   
        $flag = true;

        foreach($request->plants as $table)
        {
            if(!$this->check_array_values_uique($table))
            {
                $flag = false;
            }
            
        }
        
        // if(!$flag)
        // {
        //     return Redirect::back()->withErrors(['alert-danger', 'Plant Tag must be unique!']);
        // }
        
        //archive prev record
        $exist_records = HarvestRoom::where('room_id',$request->room_id)->where('archived','0')->get();
        foreach($exist_records as $item)
        {
            $item->archived = 1;
            $item->save();
        }
        //insert or Update
        $room = HarvestRoom::firstOrNew(['id' => $request->id]);
        $room->room_id = $request->room_id;
        $room->matrix_col = $request->matrix_col;
        $room->tables = serialize($request->plants);
        $room->user = auth()->user()->id;
        $room->archived = 0;
        $room->save();
        
        // $request->session()->flash('alert-success', 'One Harvest processed successfully!');
        // return redirect('harvest/room_builder');
        return 1;
    }

    public function check_array_values_uique($array)
    {
        return count($array) == count(array_unique($array));
    }

    public function statistic(Request $request)
    {
        $data['harvest'] = Harvest::find($request->id);
        $data['room'] = HarvestRoom::where('room_id',$data['harvest']->cultivator_company_id)->where('archived','0')->first();
        
        
        $harvest_items = $data['harvest']->items()->get();
        $room_items    = unserialize($data['room']->tables);
        $data['items'] = [];
        for($i = 0; $i < 11; $i ++)
        {
            $data['items'][$i] = 0;
            
        }
        //js data
        $js_data['plants'] = [];
        $js_data['matrix_type'] = $data['room']->matrix_col;
        $js_data['colors'][0] = "red";
        $js_data['colors'][1] = "red";
        $js_data['colors'][2] = "red";
        $js_data['colors'][3] = "red";
        $js_data['colors'][4] = "yellow";
        $js_data['colors'][5] = "yellow";
        $js_data['colors'][6] = "yellow";
        $js_data['colors'][7] = "yellow";
        $js_data['colors'][8] = "yellow";
        $js_data['colors'][9] = "green";
        $js_data['colors'][10] = "green";
        $js_data['colors'][100] = "#e2e2e2";
        $data['total_count'] = count($harvest_items);
        if($data['total_count'] == 0)
        {
            return view('errors/custom_error',['message' => 'Error.Total Item Count is Zero. No statistic generated']);
        }
        //0-3
        for($i = 0; $i < count($room_items); $i ++)
        {
            //0-2000
            for($j = 0; $j < count($room_items[$i]); $j ++)
            {
                $flag = false;
                $id = -1;
                //0-some
                for($k = 0; $k < count($harvest_items); $k ++)
                {
                    if($room_items[$i][$j] == $harvest_items[$k]['plant_tag'])
                    {
                        $id = $k;
                        $flag = true;
                    } 
                }

                if($flag)
                {
                    $js_data['plants'][$i][$j]['id']     = $j;
                    $js_data['plants'][$i][$j]['tag']    = $harvest_items[$id]['plant_tag'];
                    $js_data['plants'][$i][$j]['weight'] = $harvest_items[$id]['weight'];
                    $type = floor(($harvest_items[$id]['weight'] - 1) / 100);
                    $type = $type > 10?10:$type;
                    $js_data['plants'][$i][$j]['type']  = $type;
                }
                else
                {
                    $js_data['plants'][$i][$j] = null;
                }
            }
        }

        for($i = 0; $i < count($harvest_items); $i ++)
        {
            $type = floor(($harvest_items[$i]['weight'] - 1) / 100);
            $type = $type > 10?10:$type;
            $data['items'][$type] ++;
            
        }
        $js_data['line_count'] = 125;
        JavaScript::put($js_data);
        return view('harvest.statistic',$data);
    }

    public function _check_room_available(Request $request)
    {
        $harvest = Harvest::find($request->id);
        $room = HarvestRoom::where('room_id',$harvest->cultivator_company_id)->where('archived','0')->first();

        return $room == null?'0':'1';
    }
//-------------------------------------./Plant Room Builder---------------------------------


    public function _dry_build_one(Request $request)
    {
        $harvestDry = HarvestDry::find($request->id);
        if($harvestDry->remain_weight - $request->w < 0) return -1;
        $harvestDry->remain_weight -= $request->w;

        $curning = new Curning;
        $curning->parent_id     = $harvestDry->id;
        $curning->total_weight  = $request->w;
        $curning->remain_weight = $request->w;
        $curning->unit_weight   = $harvestDry->unit_weight;
        $curning->strain_id     = $harvestDry->strain_id;
        $curning->cultivator_company_id = $harvestDry->cultivator_company_id;
        $curning->cultivator_license_id = $harvestDry->cultivator_license_id;
        $curning->save();

        if($harvestDry->remain_weight == 0)
        {
            $harvest_id = HarvestDynamics::find($harvestDry->parent_id)->parent_id;
            $history = HarvestHistory::where('harvest_id',$harvest_id)->first();
            $history->curning = date('Y-m-d');
            $history->save();
            $harvestDry->archived = 1;
        }
            
        $harvestDry->save();

        return $harvestDry->remain_weight;
    }

    public function list_archived(Request $request)
    {
        JavaScript::put([
            'start_date' => date('m-d-Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
            'perm' => $request->perm,
        ]);

        return view('harvest/list_archived');
    }

    public function get_harvest_archived_table_data(Request $request)
    {
        $harvest = new Harvest;
        $all = isset($request->all)?$request->all:0;
        return json_encode($harvest->get_harvest_archived_table_data($request->date_range,$all));
    }

    public function get_harvest_table_data(Request $request)
    {
        $harvest = new Harvest;
        return json_encode($harvest->get_harvest_table_data($request->date_range));
    }

    public function items(Request $request)
    {
        $harvest = Harvest::find($request->i_id);
        return response()->json($harvest->items);
    }

    public function plattag_unique($plant_tag)
    {
        return count(HarvestItem::where('plant_tag',$plant_tag)->get());
    }

    public function saverow(Request $request)
    {

        $response_data = DB::transaction(function () use ($request){
            $plant_tag = $request->items[0]['plant_tag'];
            $id = $request->id;
            $mode = $request->mode;
            $items = null;
            $is_merge = false;
            
            $strain = Strainame::find($request->strain_id)->strainalias;
            $harvest_batch_id = $this->create_harvest_batch_id(date('Y-m-d'),$strain);
            $post_data = $request->except(['items','id','unit_weight','mode']);
            //if planttag is duplicate except default
            if($this->plattag_unique($plant_tag) > 0) 
            {
                if(!($mode == 'create' && $this->is_default_harvest($post_data) && $plant_tag == $this->default_plant_tag && $id == null))
                    return ['success' => 999];
            }

            $harvest = null;

            if($mode == 'create')
            {
                

                if($id == null)
                {
                    if($this->is_default_harvest($post_data))
                    {
                        $harvest = new Harvest;
                        $harvest->harvest_batch_id = $harvest_batch_id;
                        
                    }
                    else
                    {
                        $exist_harvest_id = $this->is_exist_harvest($harvest_batch_id,$request->cultivator_company_id);

                        if($exist_harvest_id == -1)
                        {
                            $harvest = new Harvest;
                            $harvest->harvest_batch_id = $harvest_batch_id;
                        }
                        else
                        {
                            $harvest = Harvest::find($exist_harvest_id);
                            $is_merge = true;
                            $items = $harvest->items()->select(['plant_tag','weight'])->get();
                        }
                    }

                    $harvest->fill($request->except(['items','mode']));   
                    $harvest->save();
                    $id = $harvest->id;
                }
                else
                {
                    $harvest = Harvest::find($id);
                }
            }
            else
            {
                $harvest = Harvest::find($id);
                $harvest->fill($request->except(['items','mode'])); 
            }

            $harvest->storeHasMany([
                'items' => $request->items
            ]); 

            //log location
            $batch_location = [[
                'barcode' => $harvest->harvest_batch_id,
                'room_id' => $harvest->cultivator_company_id,
                'status' => 0,
                'type' => 0,
                'record_id' => $harvest->id,
                'parent_id' => -1,
                'handler'   => auth()->user()->id
            ]];
            $harvest->batch_location()->delete();
            $harvest->storeHasMany(['batch_location' => $batch_location]);

            $harvest->total_weight += $request->items[0]['weight'];
            $harvest->save();
            return $res = [
                    'success' => '1',
                    'id' => $id,
                    'merge' => $is_merge,
                    'items' => $items,];
        });

        return json_encode($response_data);
    }

    public function is_default_harvest($data)
    {
        //$post_data = $request->except(['items','id','unit_weight','mode']);

        return count(array_diff($data,$this->default_havest_info)) == 0;
    }

    public function is_exist_harvest($harvest_batch_id,$cultivator_company_id)
    {
        $result = Harvest::where('harvest_batch_id',$harvest_batch_id)
        ->where('cultivator_company_id',$cultivator_company_id)
        ->first();

        return $result == null?-1:$result->id;
    }
    
    public function check_existing_record(Request $request)
    {
        $now_harvest = Harvest::find($request->harvest_id);

        if($now_harvest->cultivator_company_id == $request->cultivator_company_id && $now_harvest->strain_id == $request->strain_id && $now_harvest->cultivator_license_id == $request->cultivator_license_id)
        {
            return '0';
        }
        
        $now_havest_info = [];
        $now_havest_info['cultivator_company_id'] = $now_harvest->cultivator_company_id;
        $now_havest_info['strain_id'] = $now_harvest->strain_id;
        $now_havest_info['cultivator_license_id'] = $now_harvest->cultivator_license_id;
        
        
        $now_kind = $this->is_default_harvest($now_havest_info);
        if($now_kind) return 1;
        else return 2;
        return $now_kind;
        $existing_harvest = null;
        $id = $request->id;
        $strain = Strainame::find($request->strain_id)->strain;
        
        //retrive the harvest_batch_id
        $harvest = Harvest::find($id);
        $harvest_batch_id = $this->create_harvest_batch_id(substr(Harvest::find($id)->created_at,0,10),$strain);
    
        $existing_harvest = Harvest::where('harvest_batch_id',$harvest_batch_id)
                                    ->where('cultivator_company_id',$request->cultivator_company_id)
                                    ->where('id','!=',$id)
                                    ->first();
        $result = null;

        if($existing_harvest == null)
        {
            $result = '0###-1';
        }
        else
            if($existing_harvest->unit_weight != $request->unit_weight)
            {
                //1:flag:yes exist
                $result  = '1'.'#';
                //unit label
                $result .= Unit::find($existing_harvest->unit_weight)->name.'-';
                $result .= Unit::find($existing_harvest->unit_weight)->abbriviation;
                //unit_id
                $result .= '#'.$existing_harvest->unit_id;
                //exist record id
                $result .= '#'.$existing_harvest->id;
            }
            else
            {
                $result = '0###'.$existing_harvest->id;
            }
        
        

        return $result;
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        Harvest::find($id)->items()->delete();
        Harvest::find($id)->delete();

        return '1';
    }

    public function tracker(Request $request)
    {

    }

    public function abc(Request $request)
    {
        if($request->s == '1')
            return redirect('harvest/create')->with('success','New Harvest created Successfully');
        if($request->s == '0')
            return view('harvest/form',$data)->with('success','New Harvest updated Successfully');
    }

    private function create_harvest_batch_id($date,$strain)
    {
        return $date.'_'.$strain;
    }
}
