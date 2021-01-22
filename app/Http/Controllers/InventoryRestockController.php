<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Models
use App\Models\FGInventory;
use App\Models\InventoryVault;
use App\Models\InventoryRestockLog;
class InventoryRestockController extends Controller
{
    //
    public function index()
    {
        $inventory = FGInventory::where('status',9)->get();
        $inventory = $inventory->merge(InventoryVault::where('status',9)->get());
        return view('InventoryRestock.index',['inventory' => $inventory]);
    }
    public function getList()
    {
        return response()->json($this->generateRestocks());
    }
    public function generateRestocks()
    {
        $inventory = FGInventory::where('status',9)->get();
        $inventory = $inventory->merge(InventoryVault::where('status',9)->get());

        $response = [];
        $reasons = ['1' => 'By Order Edit','2' => 'By Order Rejection','3' => 'By Order Delete'];
        foreach($inventory as $item)
        {
            $tmp = [];
            $tmp['id'] = $item->fgasset_id;
            $tmp['metrc_tag'] = $item->metrc_tag;
            $tmp['i_type'] = $item->type;
            $tmp['strain'] = $item->Strain->strain;
            $tmp['pass1'] = 0;
            $tmp['pass2'] = 0;
            $tmp['type'] = $item->AssetType->producttype;
            $tmp['orderLabel'] = '';
            $tmp['retailer'] = '';
            $tmp['qty'] = $item->qtyonhand;
            $tmp['reason'] = '';
            if($item->rRestockLog != null)
            {
                $invoice = $item->rRestockLog->rOrder;
                if($invoice != null)
                {
                    $tmp['orderLabel']  = $invoice->number;
                    $tmp['retailer']    = $invoice->customer != null?$invoice->customer->clientname:'No Retailer Name';
                    $tmp['reason']      = $reasons[$item->rRestockLog->reason_id];
                }
            }

            $response[] = $tmp;
        }
        return $response;
    }
    public function approve(Request $request)
    {
        $cntApproved = 0;
        $cntFailed = 0;
        foreach($request->data as $item)
        {
            if($item['type'] == '1')
            {
                $inventory = FGInventory::find($item['id']);
            }
            else
            {
                $inventory = InventoryVault::find($item['id']);
            }

            if($inventory == null)
            {
                $cntFailed ++;
            }
            else
            {
                $inventory->status = 1;
                $inventory->qtyonhand = 1;
                $inventory->save();
                // remove Log
                // $inventory->rRestockLog()->delete();
            }   
            $cntApproved ++;
        }
        $inventory = $this->generateRestocks();
        return response()->json(['approved' => $cntApproved, 'failed' => $cntFailed,'inventory' => $inventory]);
    }
    public function history()
    {
        $histories = InventoryRestockLog::orderBy('created_at','desc')->get();
        $data = [];
        foreach($histories as $history)
        {
            $inventory = null;
            if($history->type == 1)
            {
                $inventory = FGInventory::find($history->fgasset_id);
            }
            if($history->type == 2)
            {
                $inventory = InventoryVault::find($history->fgasset_id);
            }
            if($inventory == null) continue;
            
            $data[] = [
                'id'            => $history->id,
                'metrc_tag'     => $inventory->metrc_tag,
                'strain'        => $inventory->Strain->strain,
                'type'          => $inventory->AssetType->producttype,
                'lOrder'        => $history->rOrder != null ? $history->rOrder->number.'/'.$history->rOrder->number2 : '',
                'lSalesrep'     => $history->rOrder != null ? $history->rOrder->SalesRepName : '',
                'restocked_at'  => $history->created_at->format('m/d/Y H:i:s'),
                'company'       => $history->rOrder != null ? $history->rOrder->CLegalName : '',
            ];
        }

        return view('inventory.restock_history', ['data' => $data]);
    }
}
