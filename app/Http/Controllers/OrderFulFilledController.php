<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//LIB
use JavaScript;
//Models
use App\Models\FGInventory;
use App\Models\InventoryVault;

use App\Models\Customer;
use App\Models\Distributor;
use App\Models\MetrcManifest;

use App\Models\InvoiceNew;
use App\Models\InvoiceItemAP;
use App\Models\InvoiceFulfilledItem;
use App\Models\InvoiceGood;
use App\Models\OurDetail;
use App\Models\Promo;
use App\Models\InvoiceContact;
use App\Models\DeliveryStatus;
use App\Models\Counter;
use App\Models\Delivery;
use App\Models\InventoryRestockLog;
use Session;
use DB;
use PDF;
use File;
use Config;
use Mail;
use App\Mail\ScheduledNotify;
class OrderFulFilledController extends OBaseController
{
    //construct
    public function __construct()
    {

    }
    public function home()
    {
        // //get fulfilled orders
        // $orders = InvoiceNew::where('status',3)->orderBy('number')->get();
        // foreach($orders as $order){
        //     // echo $order->number.'<br>';
        //     // $counter = Counter::where('key','invoice_number2')->first();
        //     // $order->number2 = $counter->prefix.$counter->value;
        //     // $counter->increment('value');
        //     $order->number = str_replace('INV','SO',$order->number);
        //     $order->save();
        // }
        $edit_permission = auth()->user()->can('order_edit')?1:0;
        JavaScript::put([
            'start_date' => date('m/01/Y'),
            'end_date' => Date('m/t/Y'),
            'edit_permission' => $edit_permission,
        ]);
        return view('orderFulfilled.home',[ 'distributors'      => Distributor::all(),
                                            'deliveries'        => Delivery::all(),
                                            'metrc_manifests'   => MetrcManifest::all()]);
    }

    public function registerDeliverySchedule(Request $request)
    {
        $date = date("Y-m-d H:i:s",strtotime($request->date));
        $invoice = InvoiceNew::find($request->id);
        $invoice->delivery_time = $date;
        $invoice->deliveryer = $request->deliveryer;
        $invoice->save();
        //mail to sales rep
        $mailData = [];
        $mailData['soNumber'] = $invoice->number;
        $mailData['invNumber'] = $invoice->number2;
        $mailData['retailer'] = $invoice->customer != null?$invoice->customer->secondaryc_name:'No Retailer Name';
        $mailData['date']   = date('m/d/Y h:i a',strtotime($invoice->delivery_time));;
        Mail::to($invoice->SalesEmail)->send(new ScheduledNotify($mailData));
        return response()->json(['success' => 1,'salesEmail' => $invoice->SalesEmail]);
    }

    public function get_list(Request $request)
    {
        $request->status = [$request->status];
        $json_data = $this->getOrdersByPagnation($request);
        return response()->json($json_data);
    }

    public function get_reject_list(Request $request)
    {
        $request->status = [5,6];
        $json_data = $this->getOrdersByPagnation1($request);
        $data = [];

        foreach($json_data['data'] as $order)
        {
            $fInfo = $order->FinancialTotalInfo;
            $nestedData = [];
            $nestedData['id']               = $order->id;
            $nestedData['number']           = $order->number;
            $nestedData['clientname']       = $order->CName;
            $nestedData['date']             = $order->date;
            $nestedData['total']            = $order->total_info['adjust_price'];
            $nestedData['rType']            = $order->DeliveryStatusName;
            $nestedData['cPName']          = $order->CPName;
            $nestedData['items']            = $order->getFulfilledItems();
            $data[] = $nestedData;
        }
        $json_data['data'] = $data;
        return response()->json($json_data);
    }

    public function delivered()
    {
        JavaScript::put([
            'start_date' => date('m/01/Y'),
            'end_date' => Date('m/t/Y'),
            'signFileUrl' => asset('storage/dSigns/'),
            'isAdmin' => auth()->user()->can('Administration')?1:0,
        ]);
        return view('orderFulfilled.delivered.home');
    }
    /**
     * Get Delivered List
     * 4.1
     */
    public function getDeliveredList(Request $request)
    {
        $request->status = [4];

        $json_data = $this->getOrdersByPagnation1($request);
        $data = [];
        foreach($json_data['data'] as $order)
        {
            $fInfo = $order->FinancialTotalInfo;
            $temp = [];
            $temp['id'] = $order->id;
            $temp['number'] = $order->number;
            $temp['number2'] = $order->number2;
            $temp['clientname'] = $order->CName;
            $temp['salesRep']   = $order->salesperson != null?
                                $order->salesperson->firstname.' '.$order->salesperson->lastname:'';
            $temp['total'] = $order->total_info['adjust_price'];
            $temp['base_price'] = $order->total_info['base_price'];
            $temp['discount'] = $order->total_info['discount'];
            $temp['tax'] = $order->total_info['tax'];
            $temp['rSubTotal'] = $fInfo['rSubTotal'];
            $temp['rTax'] = $fInfo['rTax'];
            $temp['date'] = $order->date;
            $temp['delivery_time'] = date('m/d/Y',strtotime($order->sign_date));
            $temp['logs'] = ['allowed' => [],'unallowed' => []];
            foreach($fInfo['logs'] as $key => $items)
            {
                foreach($items as $item)
                {
                    $item['amount'] = $item['amount'];
                    if($item['allowed'] == 1)
                    {
                        $temp['logs']['allowed'][] = $item;
                    }
                    if($item['allowed'] == 0)
                    {
                        $temp['logs']['unallowed'][] = $item;
                    }
                }
            }
            $data[] = $temp;
        }
        $json_data['data'] = $data;
        return response()->json($json_data);
    }
    /**
     * Undeliver Order
     */
    public function _setUndeliver(Request $request)
    {
        $order = InvoiceNew::find($request->id);
        $order->status = 3;
        $order->number2 = 'INV-'.$order->number2;
        $order->delivery_time = null;
        $order->deliveryer = null;
        $order->sign_name = null;
        $order->sign_date = null;
        $order->paid = null;
        $order->exported = null;
        $order->delivered = null;
        $order->save();
        foreach($order->fulfilledNItem as $item)
            {
                if($item->m_parent_id != -1)
                {
                    $fgItem = FGInventory::find($item->asset->fgasset_id);
                    if($fgItem == null)
                    {
                        $fgItem = InventoryVault::find($item->asset->fgasset_id);
                    }
                    if($fgItem != null)
                    {
                        if($order->sign_date == null)
                        {
                            $fgItem->qtyonhand += 1;
                            $fgItem->status = 2;
                        }
                    }
                    $fgItem->save();
                }
            }
    }
    /**
     * 4.2
     * Delivered Contact Pgae
     */

    public function deliveredPaymentView($id)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;

        $fInfo = $invoice->FinancialTotalInfo;
        $invoice['rSubTotal'] = $fInfo['rSubTotal'];
        $invoice['rTax'] = $fInfo['rTax'];
        //payment Log
        $verified = [];
        $unVerified = [];
        foreach($invoice->PaymentLog as $log)
        {
            $log->typeL = $log->type == 1?'Tax':'Sub Total';
            if($log->allowed == 1)
            {
                $verified[] = $log;
            }
            if($log->allowed == 0)
            {
                $unVerified[] = $log;
            }
        }
        $invoice['verified'] = $verified;
        $invoice['unVerified'] = $unVerified;
        $invoice['contact'] = $invoice->ContactInfo != null?$invoice->ContactInfo:new InvoiceContact;
        //contact_info
        return view('orderFulfilled.delivered.payment_view',['invoice' => $invoice,]);
    }

    public function storeInvoiceContact(Request $request)
    {
        $validatedData = $request->validate([
            'contact_person' => 'required|max:255',
            'p_date' => 'required',
            'c_sub_total' => 'required|numeric',
            'c_tax' => 'required|numeric',
        ]);
        $invoice = InvoiceNew::find($request->invoice_id);
        if($invoice->ContactInfo == null)
        {
            $invoice->ContactInfo()->save(new InvoiceContact($request->except('invoice_id','_token')));
        }
        else
        {
            $invoice->ContactInfo()->update($request->except('invoice_id','_token'));
        }
        Session::flash('message', 'Communication Notes saved correctly!');
        return redirect()->back();
    }

    public function archived()
    {
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);
        return view('orderFulfilled.archived');
    }

    public function view($id,$print)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        //return view('mailTemplate.order_fulfilled',['invoice' => $invoice]);
        return view('orderFulfilled.view',['invoice' => $invoice,'print' => $print]);
    }

    public function edit($id)
    {
        $invoice = InvoiceNew::find($id);
        $invoice->get_items_for_fullfilled_list();
        $data['invoice'] = $invoice;
        $ourdetail = OurDetail::all()->first();
        $data['invoice']['company_detail']  = $ourdetail;
        $data['invoice']['logoInvisible'] =true;
        $data['items'] = [];
        $data['promos'] = Promo::all();

        foreach($data['invoice']['items'] as $key => $item)
        {
            $temp = [];
            $temp['strain']         = $item->ap_item->strain;
            $temp['pType']          = $item->ap_item->p_type;
            $temp['strainLabel']    = $item->ap_item->StrainLabel;
            $temp['pTypeLabel']     = $item->ap_item->PTypeLabel;
            $temp['discount']       = $item->ap_item->discount;
            $temp['discount_label'] = $item->discount_label;
            $temp['discount_pro']   = $item->ap_item->discount_pro;
            $temp['discount_id']    = $item->ap_item->discount_id;
            $temp['metrc']          = $item->asset->metrc_tag;
            $temp['qty']            = $item->qty;
            $temp['units']          = $item->units;
            $temp['weight']         = $item->weight;
            $temp['unit_price']     = $item->unit_price;
            $temp['cpu']            = $item->cpu;
            $temp['base_price']     = $item->base_price;
            $temp['extended']       = $item->extended;
            $temp['tax']            = $item->tax;
            $temp['tax_note']       = $item->tax_note;
            $temp['adjust_price']   = $item->adjust_price;
            $temp['isSingle']       = $item->m_parent_id == -1?0:1;
            $temp['mergeStatus']    = 0;
            $temp['item_id']        = $item->item_id;
            $temp['asset_id']       = $item->asset_id;
            $temp['newMetrc']    = '';
            $temp['isNew']          = 0;
            $temp['deleted']        = 0;
            $temp['id']             = $item->id;
            $temp['newlyMerged']    = 1;
            $temp['child_items']    = [];
            if($temp['isSingle'] == 1)
            {
                $temp['i_type']     = $item->asset->i_type;
                $temp['fgasset_id'] = $item->asset->fgasset_id;
                $temp['um']         = $item->asset->um;
            }
            foreach($item->childItems as $child)
            {
                $tmp = [];
                $tmp['fgasset_id']  = $child->asset->fgasset_id;
                $tmp['i_type']      = $child->asset->i_type;
                $tmp['metrc']       = $child->asset->metrc_tag;
                $tmp['weight']      = $child->asset->weight;
                $tmp['newlyMerged']    = 0;
                $tmp['newMetrc']    = '';
                $result['isNew']    = 1;
                $temp['child_items'][] = $tmp;
            }
            $data['items'][] = $temp;
        }
        unset($data['invoice']['items']);
        $data['clients'] = Customer::with(['Term','basePrice'])->get();
        $data['distributors'] = Distributor::all();

        JavaScript::put([
            'invoice'   => $data['invoice'],
            'inventory' => $data['items'],
            'discounts'  => Promo::all()
        ]);
        return view('orderFulfilled.edit.edit',$data);
    }
    public function _edit_store(Request $request)
    {
        //
        return DB::transaction(function () use ($request){
            $apItems = [];
            $rInventory = $request->items;
            $invoice = InvoiceNew::find($request->id);
            foreach($rInventory as $key => $item)
            {
                $newAp = [];
                if($item['isSingle'] == 0)
                {
                    $childInventory = [];
                    foreach($item['child_items'] as $child)
                    {
                        $inventory = null;
                        if($child['i_type'] == 1)
                        {
                            $inventory = FgInventory::find($child['fgasset_id']);
                        }
                        else
                        {
                            $inventory = InventoryVault::find($child['fgasset_id']);
                        }

                        if($inventory != null)
                        {
                            $inventory->status = 1;
                            $inventory->save();
                            //restore all inventoryif deleted replace new metrc
                            if($item['deleted'] == 1 || (isset($child['deleted']) && $child['deleted'] == 1))
                            {
                                $inventory->metrc_tag = $child['newMetrc'];
                                $inventory->status = 9;
                                //logging
                                $log = new InventoryRestockLog;
                                $log->fgasset_id = $inventory->fgasset_id;
                                $log->order_id = $invoice->id;
                                $log->type = $child['i_type'];
                                $log->save();
                                $inventory->save();
                            }
                            if(!isset($child['deleted']) || $child['deleted'] == 0)
                                $childInventory[] = $inventory;
                        }
                    }
                    $newAp = [];
                    $newAp['child_items']   = $childInventory;
                    $newAp['qty']           = count($item['child_items']);
                    $newAp['strain']        = $item['strain'];
                    $newAp['p_type']        = $item['pType'];
                    $newAp['discount']      = $item['discount'];
                    $newAp['discount_id']   = $item['discount_id'];
                    $newAp['discount_type'] = 0;
                    $newAp['unit_price']    = $item['unit_price'];
                    $newAp['tax']           = 0;
                    $newAp['units']         = $item['units'];
                    $newAp['merged']        = 1;
                    $newAp['parentMetrc']   = $item['metrc'];
                }
                if($item['isSingle'] == 1)
                {
                    $inventory = null;
                    if($item['i_type'] == 1)
                    {
                        $inventory = FgInventory::find($item['fgasset_id']);
                    }
                    else
                    {
                        $inventory = InventoryVault::find($item['fgasset_id']);
                    }

                    if($inventory != null)
                    {
                        $inventory->status = 1;
                        $inventory->save();
                        if($item['deleted'] == 1)
                        {
                            //$inventory->metrc_tag = $item['newMetrc'];
                            $inventory->status = 9;
                            $inventory->save();
                            //logging
                            $log = new InventoryRestockLog;
                            $log->fgasset_id = $inventory->fgasset_id;
                            $log->order_id = $invoice->id;
                            $log->type = $item['i_type'];
                            $log->save();
                        }
                        if($item['deleted'] == 0)
                        {
                            $newAp['qty'] = 1;
                            $newAp['strain'] = $item['strain'];
                            $newAp['p_type'] = $item['pType'];
                            $newAp['discount'] = $item['discount'];
                            $newAp['discount_id'] = $item['discount_id'];
                            $newAp['discount_type'] = 0;
                            $newAp['unit_price'] = $item['unit_price'];
                            $newAp['tax'] = 0;
                            $newAp['units'] = $item['units'];
                            $newAp['merged'] = 0;
                            $newAp['child_items'] = [];
                            $newAp['child_items'][] = $inventory;
                            $newAp['parentMetrc'] = '';
                        }
                    }
                }
                //if deleted item reject on ap items
                if($item['deleted'] == 0)
                    $apItems[] = $newAp;

            }
            //delete all items
            foreach($invoice->fulfilledNItem as $f)
            {
                $f->asset()->delete();
                $f->delete();
            }
            foreach($invoice->fulfilledItem as $f)
            {
                $f->asset()->delete();
                $f->delete();
            }
            $invoice->itemAP()->delete();
            //save to invoice item ap and fulfilled item
            foreach($apItems as $ap)
            {
                $m_parent_id = 0;
                $items = $ap['child_items'];
                $merged = $ap['merged'];
                $parentMetrc = $ap['parentMetrc'];
                unset($ap['child_items']);
                unset($ap['merged']);
                unset($ap['parentMetrc']);
                $ap['invoice_id'] = $request->id;
                $item_id = InvoiceItemAP::insertGetId($ap);
                if($merged == 1)
                {
                    $merged_asset                  = [];
                    $merged_asset['strainname']    = $ap['strain'];
                    $merged_asset['asset_type_id'] = $ap['p_type'];
                    $merged_asset['metrc_tag']     = $parentMetrc;
                    $merged_asset['weight']        = 0;
                    foreach($items as $item)
                    {
                        $merged_asset['um'] = $item['um'];
                        $merged_asset['weight'] += $item['weight'];
                    }
                    $merged_asset['coa']           = $items[0]['coa'];
                    $merged_asset['upc_fk']        = $items[0]['upc_fk'];
                    $merged_asset['qtyonhand']     = count($items);
                    $merged_row = [];
                    $merged_row['invoice_id']    = $request->id;
                    $merged_row['item_id']       = $item_id;
                    $merged_row['asset_id']      = InvoiceGood::insertGetId($merged_asset);;
                    $merged_row['m_parent_id']   = -1;
                    $merged_row['scanned_metrc'] = $parentMetrc;
                    $m_parent_id = InvoiceFulfilledItem::insertGetId($merged_row);
                    $metrc_ready = 0;
                }
                $merged_row = [];
                foreach($items as $item)
                {
                    $temp = [];
                    $temp['invoice_id']    = $request->id;
                    $temp['item_id']       = $item_id;
                    $temp['m_parent_id']   = $m_parent_id;
                    $fg = null;
                    $fg = FgInventory::find($item['fgasset_id']);
                    if($fg == null)
                    {
                        $fg = InventoryVault::find($item['fgasset_id']);
                    }
                    if($fg != null)
                    {
                        $fg->status = 2;
                        $fg->save();
                        $temp['scanned_metrc'] = $item['metrc_tag'];
                        unset($fg['upc']);
                        unset($fg['strain']);
                        unset($fg['asset_type']);
                        unset($fg['description']);
                        unset($fg['scanned_metrc']);
                        unset($fg['status']);
                        unset($fg['datelastmodified']);
                        unset($fg['created_at']);
                        unset($fg['updated_at']);
                        $fg->i_type = $fg->type;
                        $temp['asset_id']      = InvoiceGood::insertGetId($fg->toarray());
                        $merged_row[] = $temp;
                    }
                }
                InvoiceFulfilledItem::insert($merged_row);
            }
            return $apItems;
        });

    }
    public function _check_metrc_info($metrc)
    {
        $inventory = FgInventory::where([['metrc_tag',$metrc],['status',1]])->first();
        if($inventory == null)
        {
            $inventory = InventoryVault::where([['metrc_tag',$metrc],['status',1]])->first();
        }
        $result = null;
        if($inventory != null)
        {
            $result = [];
            $result['i_type']      = $inventory->type;
            $result['fgasset_id']  = $inventory->fgasset_id;
            $result['strainLabel'] = $inventory->Strain->strain;
            $result['pTypeLabel']  = $inventory->AssetType->producttype;
            $result['metrc']       = $inventory->metrc_tag;
            $result['pType']       = $inventory->asset_type_id;
            $result['units']       = $inventory->Units;
            $result['weight']      = $inventory->weight;
            $result['tax_note']    = '';
            $result['qty']         = $inventory->qtyonhand;
            $result['isSingle']    = 1;
            $result['deleted']     = 0;
            $result['newMetrc']    = '';
            $result['isNew']      = 1;
            $result['mergeStatus'] = -1;
            $result['strain']      = $inventory->strainname;

        }
        return response()->json($result);
    }
    public function _update_top_info(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->customer_id = $request->clientId;
        $invoice->distuributor_id = $request->distributorId;
        $invoice->note = $request->note;
        $invoice->tax_allow = $request->tax_allow;
        $invoice->save();
        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;

        return view('orderFulfilled.edit.top',['invoice' => $invoice]);
    }
    public function delete($id)
    {
        $invoice = InvoiceNew::find($id);
        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $invoice['items'] = $invoice->fulfilledItem()->get();
        foreach($invoice['items'] as $item)
        {
            $item->strainLabel = $item->ap_item->StrainLabel;
            $item->pTypeLabel  = $item->ap_item->PTypeLabel;
            $item->childItems  = $item->childItems;
        }
        return view('orderFulfilled.delete',['invoice' => $invoice]);
    }

    public function barcode_print($id)
    {
        $data = [];
        $ourdetail = OurDetail::all()->first();
        $data['invoice'] = InvoiceNew::find($id);
        $data['invoice']['company_detail']  = $ourdetail;
        $data['coas'] =  $this->getCoaList($data['invoice']);
        $data['generator'] = new \Picqer\Barcode\BarcodeGeneratorPNG();
        return view('orderFulfilled.barcode_print',$data);
    }

    public function _set_distributor(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->distuributor_id = $request->distributor;
        $invoice->save();
        return 1;
    }
    public function _restockOrder($id)
    {
        $invoice = InvoiceNew::find($id);
        $assets = $invoice->fulfilledNItem;
        foreach($assets as $asset)
        {
            $item = $asset->asset;
            $inventory = $item->i_type == 1?FGInventory::find($item->fgasset_id):InventoryVault::find($item->fgasset_id);
            if($inventory != null)
            {
                $inventory->status = 9;
                $inventory->save();
            }
        }
        $req = new Request;
        $req->id = $id;
        $this->_remove_order($req);
        return 1;
    }
    public function _completeRejection($id)
    {
        $invoice = InvoiceNew::find($id);
        $assets = $invoice->fulfilledNItem;
        foreach($assets as $asset)
        {
            $item = $asset->asset;
            $inventory = $item->i_type == 1?FGInventory::find($item->fgasset_id):InventoryVault::find($item->fgasset_id);
            if($inventory != null)
            {
                $inventory->status = 9;
                $inventory->qtyonhand = 1;
                $inventory->save();
                //logging
                $log = new InventoryRestockLog;
                $log->fgasset_id = $inventory->fgasset_id;
                $log->order_id = $invoice->id;
                $log->type = $item->i_type;
                $log->reason_id=2;
                $log->save();
            }
        }
        $this->setDeliveryStatus($id,4);
    }
    public function _set_m_manifest(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->metrc_manifest = $request->status;
        $invoice->save();
        return 1;
    }

    public function _download_invoice_pdf($id,Request $request)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $invoice['sign_name'] = $invoice->sign_name;
        $invoice['sign_date'] = $invoice->sign_date;
        $invoice['coa_list'] = $this->getCoaList($invoice)['exist'];

        $pdf = PDF::loadView('pdfTemplate.fulfilled_invoice', ['invoice' => $invoice]);
        $pdfName = $request->name == 1?$invoice->number2:$invoice->number;
        return $pdf->download($pdfName.'.pdf');
    }

    public function _delete_store(Request $request)
    {
        $items = $request->items;
        foreach($items as $item)
        {
            if($item['m_parent_id'] == -1)
            {
                foreach($item['childItems'] as $child)
                {
                    $inventory = null;
                    if($child['asset']['i_type'] == 1)
                    {
                        $inventory = FgInventory::find($child['asset']['fgasset_id']);
                    }
                    else
                    {
                        $inventory = InventoryVault::find($child['asset']['fgasset_id']);
                    }

                    if($inventory != null)
                    {
                        $inventory->metrc_tag = $child['newMetrc'];
                        $inventory->status = 9;
                        $inventory->qtyonhand = 1;
                        $inventory->save();
                        
                        //logging
                        $log = new InventoryRestockLog;
                        $log->fgasset_id = $inventory->fgasset_id;
                        $log->order_id = $request->id;
                        $log->reason_id = 3;
                        $log->type = $child['asset']['i_type'];
                        $log->save();
                    }
                }
            }
            else
            {
                $inventory = null;
                if($item['asset']['i_type'] == 1)
                {
                    $inventory = FgInventory::find($item['asset']['fgasset_id']);
                }
                else
                {
                    $inventory = InventoryVault::find($item['asset']['fgasset_id']);
                }

                if($inventory != null)
                {
                    $inventory->status = 9;
                    $inventory->qtyonhand = 1;
                    $inventory->save();
                    
                    //logging
                    $log = new InventoryRestockLog;
                    $log->fgasset_id = $inventory->fgasset_id;
                    $log->order_id = $request->id;
                    $log->reason_id = 3;
                    $log->type = $item['asset']['i_type'];
                    $log->save();
                }
            }
        }
        $invoice = InvoiceNew::find($request->id);
        $invoice->fulfilledItem()->delete();
        $invoice->fulfilledNItem()->delete();
        $invoice->itemAP()->delete();
        $invoice->delete();
    }

    public function _email(Request $request)
    {
        $id = $request->id;
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $invoice['sign_name'] = $invoice->sign_name;
        $invoice['sign_date'] = $invoice->sign_date;
        $invoice['coa_list'] = $this->getCoaList($invoice)['exist'];

        $this->generatePdf($invoice,'pdfTemplate.fulfilled_invoice');
        if($request->mode == '1')
        {
            Mail::to($invoice->salesemail)->send(new SaleOrderSender($invoice));
        }
        if($request->mode == '2')
        {
            Mail::to($invoice['customer']->companyemail)->send(new SaleOrderSender($invoice));
        }
        if($request->mode == '3')
        {
            Mail::to($invoice['customer']->companyemail)->send(new SaleOrderSender($invoice));
            Mail::to($invoice->salesemail)->send(new SaleOrderSender($invoice));
        }
        File::delete(public_path().'/storage/'.$invoice->number.'/invoice.pdf');
        File::delete(public_path().'/storage/'.$invoice->number.'/mail.pdf');
        return $invoice['coa_list'];
    }

    public function _email_requirment_check(Request $request)
    {
        $id = $request->id;
        $invoice = InvoiceNew::find($id);

        $res = [];
        $res['status']['metrc_ready']       = $invoice->metrc_ready != null?1:0;
        $res['status']['metrc_manifest']    = $invoice->metrc_manifest == 2?1:0;
        if($invoice->customer != null)
        {
            $res['status']['term'] = $invoice->customer->term != null?1:0;
        }
        else
        {
            $res['status']['term'] = 0;
        }

        $res['status']['coa']               = count($this->getCoaList($invoice)['n_exist']) == 0?1:0;
        $res['missing_coas']                = $this->getCoaList($invoice)['n_exist'];
        $res['status']['total'] = 1;
        foreach($res['status'] as $item)
        {
            if($item == 0)
                $res['status']['total'] = 0;
        }
        return $res;
    }

    public function _remove_order(Request $request)
    {
        InvoiceNew::find($request->id)->itemAP()->delete();
        InvoiceNew::find($request->id)->fulfilledItem()->delete();
        InvoiceNew::find($request->id)->fulfilledNItem()->delete();
        InvoiceNew::find($request->id)->delete();
        return 1;
    }

    public function _set_coainbox_order(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        if($request->coainbox == '1')
            $invoice->coainbox = date('Y-m-d');
        else
            $invoice->coainbox = null;

        $invoice->save();
    }

    public function _set_paid_order(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        if($request->paid == '1')
            $invoice->paid = date('Y-m-d');
        else
            $invoice->paid = null;

        $invoice->save();
    }

    public function _set_metrc_ready_order(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        if($request->status == '1')
            $invoice->metrc_ready = date('Y-m-d');
        else
            $invoice->metrc_ready = null;

        $invoice->save();
    }

    public function _setMetrcManifest(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->m_m_str = $request->m_m_str;
        $invoice->save();
        //$request->status = 1;
        //$this->_set_metrc_ready_order($request);
        $request->status = 2;
        $this->_set_m_manifest($request);
        return 1;
    }
    /**
     * 5.10 pvHome
     */

    public function pvHome()
    {
        $data['signFileUrl'] = asset('storage/dSigns/');
        $data['verifies'] = $this->_pVerificationOrders();
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);
        return view('orderFulfilled.pvHome',$data);
    }

    public function _pVerificationOrders()
    {
        $invoices = InvoiceNew::whereHas('PaymentLog',function($query){
            $query->where('allowed',0);
        })->orderBy('sign_date','desc')->get();
        foreach($invoices as $invoice)
        {
            $invoice->clientname    = $invoice->CName;
            $invoice->companyname   = $invoice->CPName;
            $invoice->total         = $invoice->TotalInfo['adjust_price'];
            $invoice->items         = $invoice->PaymentLog()->where('allowed',0)->get();
            $invoice->creationDate  = $invoice->date;
            $invoice->date  = $invoice->SignDateD;
        }
        return $invoices;
    }
    /**
     * 5.10
     * Delivered part
     */
    public function collectPayment($id)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $data['invoice'] = $invoice;
        $data['fInfo'] = $invoice->FinancialTotalInfo;
        $data['dOptions'] = DeliveryStatus::where('id','!=',1)->get();
        return view('orderFulfilled.delivered.collect_payment',$data);
    }
}
