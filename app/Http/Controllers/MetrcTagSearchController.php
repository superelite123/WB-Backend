<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use URL;
use App\Models\InvoiceNew;
use App\Models\InvoiceFulfilledItem;
use App\Models\InvoiceGood;
use App\Models\ActiveInventory;
use App\Models\FGInventory;
use App\Models\InventoryVault;
class MetrcTagSearchController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return view('metrcSearch.index');
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'metrc' => 'required',
        ]);
        $response = ['label' => '','link' => ''];
        $metrc = $request->metrc;
        //search in Active Inventory
        $activeInventoryResult = ActiveInventory::where('metrc_tag',$metrc)->first();
        if($activeInventoryResult != null)
        {
            if($activeInventoryResult->i_type == 1)
            {
                $response['label'] = 'Inv.2 - Finished Goods';
                $response['link'] = URL::to('fginventory');
            }
            else
            {
                $response['label'] = 'Inv.1 - Bulk/Work Order';
                $response['link'] = URL::to('vaultinventory');
            }
        }
        else
        {
            //search in fulfilledItem
            $item = InvoiceFulfilledItem::whereHas('asset',function ($query) use($request) {
                $query->where('metrc_tag', $request->metrc);
            })->first();
            if($item != null)
            {
                $invoice = InvoiceNew::find($item->invoice_id);
                $response['label'] = $invoice->number;
                $response['link'] = URL::to('order_fulfilled/view/'.$invoice->id.'/0');
            }
        }
        //search in restocked inventory
        if($response['label'] == '')
        {
            $restcokInventory = FGInventory::where([['status',9],['metrc_tag',$metrc]])->first();
            if($restcokInventory == null)
            {
                $restcokInventory = InventoryVault::where([['status',9],['metrc_tag',$metrc]])->first();
            }

            if($restcokInventory != null)
            {
                $response['label'] = 'Inventory on Hold';
                $response['link'] = URL::to('invrestock');
            }
        }

        return view('metrcSearch.index',['result' => $response,'metrc' => $metrc]);
    }
}
