<?php

namespace App\Http\Controllers;
use App;
use DB;
use Storage;
use PDF;
use Mail;
use JavaScript;
use LynX39\LaraPdfMerger\Facades\PdfMerger;
use \DatePeriod;
use \DateTime;
use \DateInterval;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Counter;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Client;
use App\Models\Harvest;
use App\Models\FGInventory;
use App\Models\Artist;
use App\Models\ContactPerson;
use App\Models\Term;
use App\Models\ShippingDetail;
use App\Models\Carrier;
use App\Models\OurDetail;
use App\Models\InvoiceGood;
use App\Models\InventoryVault;
use App\Models\Distributor;
use App\Models\InvoiceCreditNoteLog;
//1.23
use App\Models\InvoiceItemAP;
use App\Models\Promo;
use App\Models\PriceMatrix;
use App\Models\Strainame;
use App\Models\Producttype;
use App\Models\InvoiceNew;
use App\Models\UPController;
use App\Models\Priority;
use App\Models\ActiveInventory;
//1.23
//1.31
use App\Models\InvoiceFulfilledItem;
//5.19
use App\Models\InvoiceOption;
use App\Helper\GC;
use File;
use App\Mail\MailSender;
use App\Mail\SaleOrderSender;
use App\Mail\PendingOrderSender;
use App\Mail\ProblemOrder;
use App\Mail\ReportNewOrder;
use Config;
class OrderController extends OBaseController
{
    //
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $coa_path = 'assets/upload/files/coa/';
    public function __construct()
    {

    }

    public function form(Request $request)
    {
        $invoice_id = $request->id;
        $carriers = Carrier::all();
        $shipping_method = new ShippingDetail;
        $shipping_method->reference = Counter::where('key','shipping_reference')->first()->value;

        $form = [
            'number'          => Counter::where('key','invoice')->first()->prefix.Counter::where('key','invoice')->first()->value,
            'date'            => date('m/d/Y'),
            'clients'         => Customer::with(['Term','basePrice'])->get(),
            'mode'            => 'store',
            'term'            => null,
            'contact_persons' => ContactPerson::getSalesPerson(),
            'distuributors'   => Distributor::all(),
            'carrier'         => Carrier::all(),
            'invoice'         => new InvoiceNew,
            'invoice_items'   => null,
            'shipping_method' => $shipping_method,
            'carriers'        => $carriers,
            'tax'             => OurDetail::all()->first()->tax,
            'strains'         => Strainame::orderby('strain')->get(),
            'producttypes'    => Producttype::where('onordermenu',1)->orderby('producttype')->get(),
            'promos'          => Promo::all(),
            'isNew'           => 0,
        ];
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
            $temp['qty']    = $i->qty - $alreay_requested->sum('qty');
            $upc = UPController::where(
                [
                    ['strain',$i->strainname],
                    ['type',$i->asset_type_id]
                ])->first();
            $weight = $upc != null?$upc->weight:0;
            $temp['weight'] = number_format((float)($i->weights - $alreay_requested->sum('qty') * $weight), 2, '.', '');
            if($temp['qty'] >= 1)
                $aInvList[] = $temp;
        }

        $form['aInvList'] = $aInvList;
        //in update case
        if($invoice_id != null)
        {
            $form['invoice']         = InvoiceNew::find($invoice_id);
            $form['invoice_items']   = $form['invoice']->itemAP()->get();
            foreach($form['invoice_items'] as $key => $item)
            {
                $form['invoice_items'][$key]->taxexempt = $item->Taxexempt;
            }
            $form['date']            = $form['invoice']->date;
            $form['number']          = $form['invoice']->number;
            $form['mode']            = 'update';
            $form['term']            = Term::find($form['invoice']->term_id);
            $form['shipping_method'] = $form['invoice']->shipping_method;
            $form['isNew']           = 1;
        }

        JavaScript::put([
            'strains'           => Strainame::all(),
            'p_types'           => Producttype::all(),
            'invoice_item'      => $form['invoice_items'],
            'mode'              => $form['mode'],
            'tax_allow'         => $form['invoice']->tax_allow,
            'id'                => $form['invoice']->id,
            'shipping_method'   => $form['shipping_method'],
            'tax'               => OurDetail::all()->first()->tax,
            'clients'           => $form['clients'],
            'sel_client'        => $form['invoice']->customer_id,
            'promos'            => $form['promos'],
            'isNew'             => $form['isNew']
        ]);

        return view('order.form',$form);
    }

    public function _form_customer_list()
    {
        return response()->json(Customer::with(['Term','basePrice'])->get());
    }
    public function _CustomerCreditNoteTotal(Request $request)
    {
        $customer = Customer::find($request->id);
        return response()->json(['total' => $customer->CreditNoteTotal]);
    }
    public function _form_avaliable_qty(Request $request)
    {
        $strain = $request->strain;
        $p_type = $request->p_type;
        $res = [];
        $res['qty']       = 0;
        $res['weight']    = 0;
        $res['taxexempt'] = -1;
        $upc = UPController::where(
            [
                ['strain',$strain],
                ['type',$p_type]
            ])->first();
        $weight = $upc != null?$upc->weight:0;
        $basePrice = $upc != null?$upc->baseprice:0;
        $fg     = FGInventory::where([
                ['strainname',$strain],
                ['asset_type_id','=',$p_type],
                ['qtyonhand','>',0],['status','=',1],
                ])->get();
        $vault  = InventoryVault::where([
            ['strainname',$strain],
            ['asset_type_id',$p_type],
            ['qtyonhand','>',0],
            ['status','=',1]])->get();
        $alreay_requested = InvoiceItemAP::whereHas('Order', function($q) {
                                $q->whereIn('status', [0,1,2]);
                            })->where([
                                    ['strain',$strain],
                                    ['p_type',$p_type],
                                    ['invoice_id','!=',$request->id]
                                ])->get();
        $res['qty']    = count($fg) + count($vault) - $alreay_requested->sum('qty');
        $res['weight'] = $fg->sum('weight') + $vault->sum('weight') - ($weight * $alreay_requested->sum('qty'));
        $res['weight'] = number_format((float)$res['weight'], 2, '.', '');
        $res['taxexempt'] = $upc != null?$upc->taxexempt:-1;
        $res['basePrice'] = $basePrice;

        return response()->json($res);
    }
    //Jar Flower 3.5G Case Pack of (32)
    public function store(Request $request)
    {
        $invoice = DB::transaction(function () use ($request){
            $mode = $request->mode;

            $invoice_items = [];

            $items = $request->items;

            if($mode == 'store')
            {
                $invoice = new InvoiceNew;
                Counter::where('key','invoice')->first()->increment('value');
                Counter::where('key','shipping_reference')->first()->increment('value');
                $shipping_method = ShippingDetail::create($request->shipping_method);
            }
            else
            {
                $invoice = InvoiceNew::with('shipping_method')->find($request->id);
                $shipping_method = $request->shipping_method;
                unset($shipping_method['ok']);
                unset($shipping_method['id']);
            }

            //save invoice info
            $invoice->fill($request->except(['items','shipping_method']));
            if($invoice->status == Config::get('constants.order.unable_fulfillment'))
            {
                $invoice->status = Config::get('constants.order.fulfillment');
            }
            $invoice->date = date('Y-m-d',strtotime($request->date));
            $invoice->credit_amount = $request->creditNoteForDeduct;
            $invoice->tax_type = 2;
            $invoice->save();

            //store or update shipping info
            if($mode == 'store'){
                $invoice->shipping_method()->save($shipping_method);
            }
            else{
                $invoice->shipping_method()->update($shipping_method);
            }

            //store or update items
            $invoice->itemAP()->delete();
            $invoice->storeHasMany(['itemAP' => $items]);
            //check credit note deduct
            $customer = Customer::find($request->customer_id);
            $creditNotes = $customer->rCreditNote()->where('archive','0')->get();
            $requestedCreditNote = $request->creditNoteForDeduct;
            $bBreak = false;
            foreach($creditNotes as $creditNote)
            {
                $diff = $requestedCreditNote - $creditNote->total_price;
                $amountForLog = 0;
                if($diff >= 0)
                {
                    $amountForLog = $creditNote->total_price;
                    $requestedCreditNote -= $amountForLog;
                    //archive this creditNote
                    $creditNote->total_price = 0;
                    $creditNote->archive = 1;
                    $creditNote->save();
                }
                else
                {
                    $amountForLog = $requestedCreditNote;
                    $creditNote->total_price -= $requestedCreditNote;
                    $creditNote->save();
                    $bBreak = true;
                }
                //log
                $log = new InvoiceCreditNoteLog;
                $log->credit_note = $creditNote->id;
                $log->invoice_id = $invoice->id;
                $log->amount = $amountForLog;
                $log->save();
                if($bBreak) break;
            }
            //update total info
            $totalInfo = $invoice->TotalInfo;
            $invoice->update([
                'tbp' => $totalInfo['base_price'],
                'discount' => $totalInfo['discount'],
                'e_discount' => $totalInfo['e_discount'],
                'extended' => $totalInfo['extended'],
                'promotion' => $totalInfo['promotion'],
                'pr_value' => $totalInfo['prValue'],
                'tax' => $totalInfo['tax'],
                'total' => $totalInfo['adjust_price'],
                'qty' => $totalInfo['qty'],
            ]);
            return $invoice;
        });

        return response()
            ->json($invoice->id);
    }
    public function pending_uppermanage_email($id)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;

        $invoice['items']           = $invoice->itemAP()->get();
        $invoice['salesperson']     = $invoice->salesperson;
        $invoice['customer']        = $invoice->customer()->with('state_name')->first();
        $invoice['shipping_method'] = $invoice->shipping_method;
        $invoice['coa_list'] = [];
        $invoice['url'] = App::make('url')->to('/');
        $invoice['url'] .= '/order/pending_list';
        $this->generate_sales_pdf($invoice);

        $uppermanagers = ContactPerson::where('uppermanage',1)->get();
        foreach($uppermanagers as $manager)
        {
            if (filter_var($manager->email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($manager->email)->send(new ReportNewOrder($invoice));
            }
        }
        File::delete(public_path().'/storage/'.$invoice->number.'/invoice.pdf');
        File::delete(public_path().'/storage/'.$invoice->number.'/mail.pdf');
        return 1;
    }
    public function pending_list(Request $request)
    {
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);

        return view('order.pending_list',['priorities' => Priority::all()]);
    }

    public function get_pending_list(Request $request)
    {
        $invoice = new InvoiceNew;
        $orders = $this->getOrdersByDateRange($request->date_range,Config::get('constants.order.pending'),-1,1);

        foreach($orders as $order)
        {
            $order['items']                 = $order->itemAP()->get();
            $order->clientname              = $order->CName;
            $order->companyname             = $order->CPName;
            $order->total_info              = $order->TotalInfo;
            /**get extra discount**/
            $order->pDiscount               = $order->rPDiscount;
            foreach($order['items'] as $item)
            {
                $item->description   = $item->StrainLabel.','.$item->PTypeLabel;
                $item->sub_total     = $item->BasePrice;
                $item->cpu           = $item->CPU;
                $item->less_discount = $item->Extended;
                $item->adjust_price  = $item->AdjustPrice;
                $item->discount_label= $item->DisType;
                $item->tax_note      = $item->TNote;
            }
        }

        return json_encode($orders);
    }
    public function _pendingOrderCustomerDetail(Request $request)
    {
        $order = InvoiceNew::find($request->id);
        $response = ['customerFinacialInfo' => ['myInvoices' => []],'pendingOrders' => []];
        if($order != null)
        {
            $response['customerFinacialInfo']    = $order->customer->FinacialInfo;
            $response['pendingOrders']           = $order->customer->Invoices()->where([['status',0]])->get();
            foreach($response['pendingOrders'] as $item)
            {
                $item->total_info  = $order->TotalInfo;
            }
        }
        return response()->json($response);
    }
    public function pending_detail($id,$print)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;

        $invoice['items']           = $invoice->itemAP()->get();
        $invoice['salesperson']     = $invoice->salesperson;
        $invoice['customer']        = $invoice->customer()->with('state_name')->first();
        $invoice['shipping_method'] = $invoice->shipping_method;

        return view('order.pending_detail',['invoice' => $invoice,'print' => $print]);
    }

    public function _send_pending_fulfillment(Request $request)
    {
        $order = InvoiceNew::find($request->id);
        if($order == null) return 0;
        $order->status = Config::get('constants.order.fulfillment');
        $order->save();
        /**
         * if contact person is upper manager
         * send email him
         */
        $this->pending_uppermanage_email($order->id);
        return 1;
    }

    public function _pending_email(Request $request)
    {
        $id = $request->id;
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;

        $invoice['items']           = $invoice->itemAP()->get();
        $invoice['salesperson']     = $invoice->salesperson;
        $invoice['customer']        = $invoice->customer()->with('state_name')->first();
        $invoice['shipping_method'] = $invoice->shipping_method;
        $invoice['coa_list'] = [];
        $this->generate_sales_pdf($invoice);
        Mail::to($invoice['customer']->companyemail)->send(new PendingOrderSender($invoice));
        File::deleteDirectory(public_path().'/storage/'.$invoice->number);
        return 1;
    }

    public function _set_priority(Request $request)
    {
        $order = InvoiceNew::find($request->id);
        $order->priority_id = $request->priority;
        $order->save();
    }

    public function _AddDiscount(Request $request)
    {
        //find previous discount options
        $option = InvoiceOption::where(['order_id' => $request->id,'type' => 1])->first();
        if($option == null) $option = new InvoiceOption;

        $option->order_id = $request->id;
        $option->value   = $request->amount;
        $option->note     = $request->note;
        $option->type     = 1;
        $option->save();
        return;
    }

    public function fulfillment_list(Request $request)
    {
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);

        return view('order.fulfillment_list');
    }

    public function get_fulfillment_list(Request $request)
    {
        $orders = $this->get_order_range($request->date_range,Config::get('constants.order.fulfillment'),-1,1);
        foreach($orders as $order)
        {
            $order['items'] = $order->itemAP()->get();
            $order->clientname  = $order->CName;
            $order->companyname = $order->CPName;
            $order->total_info  = $order->TotalInfo;
            $order->priority    = $order->Priority;
            //get extra discount
            $order->pDiscount               = $order->rPDiscount;
            foreach($order['items'] as $item)
            {
                $item->description   = $item->StrainLabel.','.$item->PTypeLabel;
                $item->sub_total     = $item->BasePrice;
                $item->cpu           = $item->CPU;
                $item->less_discount = $item->Extended;
                $item->adjust_price  = $item->AdjustPrice;
                $item->discount_label= $item->DisType;
                $item->tax_note      = $item->TNote;
            }
            $order['shipping_method'] = $order->shipping_method()->with('carrier')->get();
        }
        return json_encode($orders);
    }

    public function get_fulfillment_problematic_list(Request $request)
    {
        $orders = $this->get_order_range($request->date_range,Config::get('constants.order.unable_fulfillment'));
        foreach($orders as $order)
        {
            $order['items'] = $order->itemAP()->get();
            $order->clientname  = $order->CName;
            $order->companyname = $order->CPName;
            $order->total_info  = $order->TotalInfo;
            foreach($order['items'] as $item)
            {
                $item->description   = $item->StrainLabel.','.$item->PTypeLabel;
                $item->sub_total     = $item->BasePrice;
                $item->cpu           = $item->CPU;
                $item->less_discount = $item->Extended;
                $item->adjust_price  = $item->AdjustPrice;
                $item->discount_label= $item->DisType;
                $item->tax_note      = $item->TNote;
            }
            $order['shipping_method'] = $order->shipping_method()->with('carrier')->get();
        }
        return json_encode($orders);
    }

    public function _send_problem_salesPerson(Request $request)
    {
        //url :http://localhost:8000/order/form?id=320
        $id = $request->id;
        $invoice = InvoiceNew::find($id);

        $mail_data = [];
        $mail_data['url'] = App::make('url')->to('/');
        $mail_data['url'] .= '/order/form?id='.$id;
        $mail_data['inv'] = $invoice->number;
        $mail_address = $invoice->salesperson != null?$invoice->salesperson->email:null;
        if($mail_address == null)
        {
            return -1;
        }
        else
        {
            if($mail_address == '')
            {
                return -2;
            }
        }

        Mail::to($mail_address)->send(new ProblemOrder($mail_data));
    }

    public function get_order_range($date_range,$status,$paid = -1,$priority_check = -1)
    {
        if($date_range == null)
        {
            $date_range['start_date'] = date('Y-m-d', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->change_date_format($date_range);
        }
        $cond = InvoiceNew::with('salesperson')->where('status',$status)
               ->whereRaw('DATE(date) >= ?', [$date_range['start_date']])
               ->whereRaw('DATE(date) <= ?', [$date_range['end_date']]);
        if($paid == 1)
        {
            $cond = $cond->where('paid','!=',null);
        }
        if($priority_check == 1)
        {
            $cond = $cond->orderBy('priority_id');
        }
        return $cond->orderby('created_at','desc')->get();
    }
    private function change_date_format($date_range)
    {
        if($date_range == null)
        {
            $date_range['start_date'] = date('Y-m-d', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
            return $date_range;
        }

        $date_range = str_replace(' ', '', $date_range);

        $tmp = [];
        $tmp[0] = explode('-',$date_range);
        $tmp[1] = explode('/',$tmp[0][0]);
        $tmp[2] = explode('/',$tmp[0][1]);
        $result['start_date'] = $tmp[1][2].'-'.$tmp[1][0].'-'.$tmp[1][1];
        $result['end_date']   = $tmp[2][2].'-'.$tmp[2][0].'-'.$tmp[2][1];

        return $result;
    }
    public function generate_sales_pdf($invoice,$view_name = 'invoice_template')
    {
        $view_name = 'order.'.$view_name;
        $pdf = PDF::loadView($view_name, ['invoice' => $invoice]);
        $file_name = $invoice->number.'/invoice.pdf';

        $file_name_mail = $invoice->number.'/mail.pdf';
        if(!Storage::disk('public')->put($file_name, $pdf->output()))
        {
            return false;
        }

        if(!Storage::disk('public')->put($file_name_mail, $pdf->output()))
        {
            return false;
        }
    }
    private function check_inventory_duplicate($temp,$item)
    {
        foreach($temp as $i => $t)
        {
            if($t->fgasset_id == $item->fgasset_id)
            {
                return $i;
            }
        }
        return -1;
    }
    public function fulfillment_form(Request $request)
    {
        $data = [];
        /**
         * Customer Name,Order Date,Order Number,Sales Person,Distributor,Term
         * Order Note,Fullfillment Note
         * Order sales request
         * Inventory for Invoice
         */
        $order = InvoiceNew::find($request->id);
        if($order == null)
        {
            return view('errors.500',['title' => "We cann't requested Order",'content' => "We cann't find requested Order"]);
        }

        $order['req_items'] = $order->itemAP()->get();

        foreach($order['req_items'] as $item)
        {
            $item['strain_label']  = $item->StrainLabel;
            $item['type_label']    = $item->PTypeLabel;
            $item['base_price']    = $item->BasePrice;
            $item['cpu']    = $item->CPU;
            $item['extended']      = $item->Extended;
            $item['discount_type'] = $item->DisType;
            $item['tax_note']      = $item->TNote;
            $item['adjust_price']  = $item->AdjustPrice;
            $item['fulfilled_cnt'] = 0;
        }
        $order->clientname  = $order->CName;
        $order->companyname = $order->CPName;
        //Inventory for Invoice
        $inventory = [];
        $fg = new FgInventory;
        $vault = new InventoryVault;
        $temp = [];
        /**
         * per request pick equal qty oldest items from
         * fg and vault
         */
        $alls = [];
        foreach($order['req_items'] as $key => $item)
        {
            $temp = $fg->get_items($item->strain,$item->p_type);
            $temp = array_merge($temp,$vault->get_items($item->strain,$item->p_type));
            foreach($alls as $t )
            {
                $r = $this->check_inventory_duplicate($temp,$t);
                if($r != -1)
                {
                    //unset($temp[$r]);
                    array_splice($temp, $r, 1);
                }
            }
            for($i = 0; $i < count($temp) - 1; $i ++)
            {
                for($j = $i ++; $j < count($temp); $j ++)
                {
                    if($temp[$i]->harvested_date < $temp[$j]->harvested_date)
                    {
                        $val = $temp[$i];
                        $temp[$i] = $temp[$j];
                        $temp[$j] = $val;
                    }
                }
            }
            //pick only 3 oldest items
            $cnt = $item->qty;
            $inventory[$key]['item_id']     = $item->id;
            $inventory[$key]['strain']      = $item->strain;
            $inventory[$key]['p_type']      = $item->p_type;
            $inventory[$key]['qty']         = $item->qty;
            $inventory[$key]['merge_info']  = ['status' => 0,'metrc' => ''];
            $inventory[$key]['items']       = count($temp) < $item->qty?$temp:array_slice($temp,0,$item->qty);
            $alls = array_merge($alls,$inventory[$key]['items']);
        }

        JavaScript::put([
            'invoice_id' => $request->id,
            'inventory' => $inventory,
            'req_items' => $order['req_items']
        ]);
        return view('order.fulfillment_form',['invoice_id' => $request->id,'order' => $order,'inventory' => $inventory]);
    }

    public function _check_metrc_info(Request $request)
    {
        $metrc = $request->metrc;
        $res = FgInventory::where('metrc_tag',$metrc)->first();
        if($res == null)
        {
            $res = InventoryVault::where('metrc_tag',$metrc)->first();
        }

        if($res == null) return -1;
        $data['strain'] = $res->strainname;
        $data['p_type'] = $res->asset_type_id;
        $data['id']     = $res->fgasset_id;
        $data['i_type'] = $res->type;
        $data['coa']    = $res->CoaName;
        return response()->json($data);
    }

    public function _print_barcode(Request $request)
    {
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode_html = "";
        $cnt = 0;
        $style = 'margin-bottom:15px;';
        foreach($request->metrcs as $metrc)
        {
            if($cnt == 3)
            //$style .= 'margin-left:150px;';
            $style .= 'margin-left:0px;';
            //batchid
            $barcode_html .= '<div style='.$style.'><img src="data:image/png;base64,' . base64_encode(
            $generator->getBarcode($metrc, $generator::TYPE_CODE_128)) . '">';
            $barcode_html .= '<br>';
            $barcode_html .= '<span style="font-size:8px;text-align:center">'.$metrc.'</span></div>';
            $cnt ++;
        }

        return response()->json($barcode_html);
    }

    public function _fulfillment_store(Request $request)
    {
        return DB::transaction(function () use ($request){
            $order_id        = $request->id;
            $order_status    = $request->status;
            $order_inventory = $request->inventory;
            //save order status
            $order = InvoiceNew::find($order_id);

            $order->status = $order_status;
            $counter = Counter::where('key','invoice_number2')->first();
            $order->number2 = $counter->prefix.$counter->value;
            $counter->increment('value');
            $order->save();
            if($order_status == Config::get('constants.order.unable_fulfillment'))
            {
                return 2;
            }
            //save selected order's items
            $item_data = [];
            $m_parent_id = 0;
            $order->fulfilledItem()->with('asset')->delete();
            foreach($order_inventory as $sub_inventory)
            {
                //if merged
                if($sub_inventory['merge_info']['status'] == 1)
                {
                    //store new merged asset to InvoiceGood
                    $merged_asset                  = [];
                    $merged_asset['strainname']    = $sub_inventory['strain'];
                    $merged_asset['asset_type_id'] = $sub_inventory['p_type'];
                    $merged_asset['metrc_tag']     = $sub_inventory['merge_info']['metrc'];
                    $merged_asset['qtyonhand']     = count($sub_inventory['items']);
                    //parent's um is sum of child's um
                    $merged_asset['um'] = 0;
                    foreach($sub_inventory['items'] as $item)
                    {
                        $merged_asset['um'] += $item['um'];
                    }
                    $merged_asset['weight']        = 0;
                    foreach($sub_inventory['items'] as $item)
                    {
                        $merged_asset['weight'] += $item['weight'];
                    }
                    $merged_asset['coa']           = $sub_inventory['items'][0]['coa'];
                    $merged_asset['upc_fk']        = $sub_inventory['items'][0]['upc_fk'];

                    $merged_row = [];
                    $merged_row['invoice_id']    = $request->id;
                    $merged_row['item_id']       = $sub_inventory['item_id'];
                    $merged_row['asset_id']      = InvoiceGood::insertGetId($merged_asset);;
                    $merged_row['m_parent_id']   = -1;
                    $merged_row['scanned_metrc'] = $sub_inventory['merge_info']['metrc'];

                    $m_parent_id = InvoiceFulfilledItem::insertGetId($merged_row);
                }
                $merged_row = [];
                $iGood_del_row_ids = [];
                foreach($sub_inventory['items'] as $key => $item)
                {
                    $temp = [];
                    $temp['invoice_id']    = $request->id;
                    $temp['item_id']       = $sub_inventory['item_id'];
                    $temp['m_parent_id']   = $m_parent_id;
                    $fg = null;
                    if($item['i_type'] == 1)
                    {
                        $fg = FgInventory::find($item['fgasset_id']);
                    }
                    if($item['i_type'] == 2)
                    {
                        $fg = InventoryVault::find($item['fgasset_id']);
                    }
                    if($fg != null)
                    {
                        $fg->status = 2;
                        $fg->save();
                    }
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
                InvoiceFulfilledItem::insert($merged_row);
                // InvoiceGood::whereIn('fgasset_id',$iGood_del_row_ids)->delete();
                // InvoiceGood::insert($sub_inventory['items']);
                $m_parent_id = 0;
            }
            return 3;
        });
    }

    public function fulfilled_detail($id,$print)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        return view('order.fulfilled_detail',['invoice' => $invoice,'print' => $print]);
    }

    public function fulfilled_list()
    {
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);

        return view('order.fulfilled_list',['distributors' => Distributor::all(),]);
    }
    public function _set_distributor(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->distuributor_id = $request->distributor;
        $invoice->save();
        return 1;
    }
    public function get_fulfilled_list(Request $request)
    {
        $orders = $this->get_order_range($request->date_range,Config::get('constants.order.fulfilled'));
        foreach($orders as $order)
        {
            $order->get_items_for_fullfilled_list();
        }

        return response()->json($orders);
    }
    public function _download_invoice_pdf($id)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $invoice['sign_name'] = $invoice->sign_name;
        $invoice['sign_date'] = $invoice->sign_date;
        $invoice['coa_list'] = $this->get_coa_list($invoice)['exist'];

        $pdf = PDF::loadView('order.fulfilled_invoice_template', ['invoice' => $invoice]);
        return $pdf->download($invoice->number.'.pdf');

        //return Response::download();
    }
    public function fulfilled_edit($id)
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
        return view('order.fulfilled_edit',['invoice' => $invoice]);
    }

    public function _fulfilled_edit_store(Request $request)
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
                        $inventory->status = 1;
                        $inventory->save();
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
                    $inventory->status = 1;
                    $inventory->save();
                }
            }
        }
        $invoice = InvoiceNew::find($request->id);
        $invoice->fulfilledItem()->delete();
        $invoice->fulfilledNItem()->delete();
        $invoice->itemAP()->delete();
        $invoice->delete();
    }

    public function _fulfilled_email(Request $request)
    {
        $id = $request->id;
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $invoice['sign_name'] = $invoice->sign_name;
        $invoice['sign_date'] = $invoice->sign_date;
        $invoice['coa_list'] = $this->get_coa_list($invoice)['exist'];

        $this->generate_sales_pdf($invoice,'fulfilled_invoice_template');
        Mail::to($invoice->customer->companyemail)->send(new SaleOrderSender($invoice));
        File::delete(public_path().'/storage/'.$invoice->number.'/invoice.pdf');
        File::delete(public_path().'/storage/'.$invoice->number.'/mail.pdf');
        return $invoice['coa_list'];
    }

    public function get_coa_list($invoice)
    {
        $coas = ['exist' => [],'n_exist' => []];
        foreach ($invoice->fulfilledItem as $i => $item)
        {
            foreach ($item->CoaList as $coa)
            {
                if ($coa['is_exist'])
                {
                    $exist = false;
                    foreach($coas['exist'] as $ncoa)
                    {
                        if($ncoa == $coa['coa'])
                        $exist = true;
                    }
                    if(!$exist)
                        $coas['exist'][] = $coa['coa'];
                }
                else
                {
                    $exist = false;
                    foreach($coas['n_exist'] as $ncoa)
                    {
                        if($ncoa == $coa['coa'])
                        $exist = true;
                    }
                    if(!$exist)
                        $coas['n_exist'][] = $coa['coa'];

                    // if(!array_search($coa['coa'],$coas['n_exist']))
                    //     $coas['n_exist'][] = $coa['coa'];
                }
            }
        }

        return $coas;
    }

    public function _email_coa_missing_check(Request $request)
    {
        $id = $request->id;
        $invoice = InvoiceNew::find($id);

        return $this->get_coa_list($invoice)['n_exist'];
    }

    public function _remove_order(Request $request)
    {
        InvoiceNew::find($request->id)->itemAP()->delete();
        InvoiceNew::find($request->id)->fulfilledItem()->delete();
        InvoiceNew::find($request->id)->fulfilledNItem()->delete();
        InvoiceNew::find($request->id)->delete();
        return 1;
    }

    public function signature_list(Request $request)
    {
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);

        return view('order.signature_list');
    }

    public function get_sign_list(Request $request)
    {
        $orders = $this->get_order_range($request->date_range,Config::get('constants.order.fulfilled'));
        foreach($orders as $order)
        {
            $order->get_items_for_fullfilled_list();
        }

        return response()->json($orders);
    }

    public function signature_panel(Request $request)
    {
        $id = $request->id;
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;

        return view('order.signature_panel',['invoice' => $invoice]);
    }

    public function _save_sign(Request $request)
    {
        $flag =  DB::transaction(function () use ($request){
            $invoice = InvoiceNew::find($request->id);
            foreach($invoice->fulfilledNItem as $item)
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
                        if($invoice->sign_date == null)
                        {
                            $fgItem->qtyonhand -= 1;
                            $fgItem->status = 3;
                        }
                    }
                    $fgItem->save();
                }
            }

            $invoice->sign_name = $request->sign_name;
            $invoice->sign_date = $request->sign_date;
            $invoice->save();
            $result = array();
            $imagedata = base64_decode($request->img_data);
            $filename = 'sign';

            //Location to where you want to created sign image
            $file_name = $invoice->number.'/'.$filename.'.png';

            if(!Storage::disk('public')->put($file_name, $imagedata))
            {
                return false;
            }
            //set as delivered
            $request->delivered = 1;
            $this->_set_delivered_order($request);
            return '1';
        });
        return $flag;
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

    public function _set_delivered_order(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        if($request->delivered == '1')
            $invoice->delivered = date('Y-m-d');
        else
            $invoice->delivered = null;

        $invoice->save();
    }

    public function _save_deliver_note(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->deliver_note = $request->note;
        $invoice->save();
    }

    public function report(Request $request)
    {
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);

        return view('order.report_list');
    }

    public function _get_report_list(Request $request)
    {
        $orders = [];
        $date_range = $this->change_date_format($request->date_range);
        switch($request->flag)
        {
            case '-1':
                $orders = InvoiceNew::where('status',Config::get('constants.order.fulfilled'))
                            ->where('paid',null)
                            ->whereRaw('DATE(date) >= ?', [$date_range['start_date']])
                            ->whereRaw('DATE(date) <= ?', [$date_range['end_date']])->get();
                foreach($orders as $order){
                    $order->get_items_for_fullfilled_list();
                }
            break;
            case '0':
                $orders = $this->get_paid_orders($date_range);
            break;
            case '1':
                $orders = $this->get_overdue_orders($date_range);
            break;
            case '2':
                $orders = $this->get_overdue_orders($date_range,15);
            break;
            case '3':
                $orders = $this->get_overdue_orders($date_range,30);
            break;
            case '4':
                $orders = $this->get_overdue_orders($date_range,60);
            break;
            case '5':
                $orders = $this->get_overdue_orders($date_range,90);
            break;
        }
        $chart_data = [];
        $period = new DatePeriod(
            new DateTime($date_range['start_date']),
            new DateInterval('P1D'),
            new DateTime($date_range['end_date'])
        );
        $key = 0;
        foreach ($period as $value) {
            $r_date = $value->format('Y-m-d');
            $is_exist = false;
            $chart_data['d_labels'][$key]['label']  = $r_date;
            $chart_data['bases'][$key]['value']     = 0;
            $chart_data['discounts'][$key]['value'] = 0;
            $chart_data['taxs'][$key]['value']      = 0;

            foreach($orders as $order)
            {
                $compare_date = '';
                if($request->flag == -1)
                    $compare_date = $order->date;

                if($request->flag == 0)
                    $compare_date = $order->paid;
                if($request->flag != -1 && $request->flag != 0)
                    $compare_date = $order->o_date;

                if($r_date == $compare_date)
                {
                    $is_exist = true;

                    $chart_data['bases'][$key]['value']     += $order->total_info['base_price'];
                    $chart_data['discounts'][$key]['value'] += $order->total_info['discount'];
                    $chart_data['taxs'][$key]['value']      += $order->total_info['tax'];
                }
            }

            if(!$is_exist)
            {
                unset($chart_data['d_labels'][$key]);
                unset($chart_data['bases'][$key]);
                unset($chart_data['discounts'][$key]);
                unset($chart_data['taxs'][$key]);
            }
            else{
                $key ++;
            }
        }
        foreach($orders as $order)
        {
            $chart_data['labels'][]['label']  = $order->number;
            $chart_data['weights'][]['value'] = $order->total_info['weight'];
            //$total_weight
        }
        return response()->json(['orders' => $orders,'chart_data' => $chart_data]);
    }

    public function get_paid_orders($date_range)
    {
        $orders = [];
        $orders = InvoiceNew::whereIn('status',[Config::get('constants.order.fulfilled'),Config::get('constants.order.archived')])
               ->whereRaw('DATE(paid) >= ?', [$date_range['start_date']])
               ->whereRaw('DATE(paid) <= ?', [$date_range['end_date']])->get();
        foreach($orders as $key => $order)
        {
            $order->get_items_for_fullfilled_list();
        }
        return $orders;
    }

    public function get_overdue_orders($date_range,$d = 0)
    {
        $orders = [];
        $orders = InvoiceNew::select('invoices_new.*','t.*',DB::raw('DATE_ADD(
            DATE_ADD(invoices_new.date,INTERVAL t.days DAY),INTERVAL '.$d.' DAY) as o_date'))
                  ->leftjoin('terms as t','invoices_new.term_id','=','t.term_id')
                  ->where('paid',null)
                  ->whereIn('status',[Config::get('constants.order.fulfilled'),Config::get('constants.order.archived')])
                  ->whereRaw('DATE_ADD(
                              DATE_ADD(invoices_new.date,INTERVAL t.days DAY),INTERVAL '.$d.' DAY) >= ?',
                              [$date_range['start_date']])
                  ->whereRaw('DATE_ADD(
                              DATE_ADD(invoices_new.date,INTERVAL t.days DAY),INTERVAL '.$d.' DAY) <= ?',
                              [$date_range['end_date']])
                  ->get();
        foreach($orders as $key => $order)
        {
            $order->get_items_for_fullfilled_list();
            $dd = date('Y-m-d',strtotime($order->PayDate." +".$d." days"));
            $daysLeft = abs(strtotime(date('Y-m-d')) - strtotime($dd));
            $order->diff_date = $daysLeft/(60 * 60 * 24);
        }
        return $orders;
    }
}
