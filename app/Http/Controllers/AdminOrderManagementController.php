<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\InvoiceNew;
use App\Models\FGInventory;
use App\Models\InvoiceGood;
class AdminOrderManagementController extends Controller
{
    //
    public function __construct()
    {

    }

    public function index()
    {
        return view('admin_order_management.index');
    }

    public function find(Request $request)
    {
        $order = InvoiceNew::find($request->id);
        $inventories = [];
        if($order != null)
        {
            $fulfilledItems = $order->fulfilledItem()->get();
            foreach($fulfilledItems as $fItem)
            {
                $asset_id = $fItem->asset != null ? $fItem->asset->fgasset_id : 0;
                
                $subInventory = [];
                
                if($fItem->m_parent_id == -1)
                {
                    foreach($fItem->childItems as $cItem)
                    {
                        $c_asset_id = $cItem->asset != null ? $cItem->asset->fgasset_id : 0;
                        
                        $inventory = FGInventory::find($c_asset_id);

                        if($inventory != null)
                        {
                            $subInventory[] = $inventory;
                        }
                    }

                }
                
                $inventory = FGInventory::find($asset_id);
                if($fItem->m_parent_id == -1)
                {
                    $inventory = InvoiceGood::find($fItem->asset_id);
                }
                if($inventory != null)
                {
                    $inventory->cItems = $subInventory;
                    $inventories[] = $inventory;
                }
            }
        }
        $data['inventories'] = $inventories;
        return view('admin_order_management.find', $data);
    }
}
