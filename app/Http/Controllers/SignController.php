<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//Functions
use \DatePeriod;
use \DateTime;
use \DateInterval;
//Library
use DB;
use Storage;
use JavaScript;
//Models
use App\Models\InvoiceNew;
use App\Models\FGInventory;
use App\Models\InventoryVault;
use App\Models\DeliveryStatus;
use App\Models\OurDetail;
use App\Models\InvoicePaymentLog;
use App\Mail\ReportOrderDelivery;
use App\Mail\SignSalesPerson;
use App\Models\Counter;
//Config
use Config;
use Mail;
use File;
class SignController extends OBaseController
{
    //construct
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        JavaScript::put([
            'start_date' => date('m/01/Y'),
            'end_date' => Date('m/t/Y'),
            'deliveries' => DeliveryStatus::all(),
            'delivered_status' => 2
        ]);

        return view('sign.home');
    }

    public function get_list(Request $request)
    {
        $request->status = [3];
        $json_data = $this->getOrdersByPagnation1($request);
        $data = [];

        foreach($json_data['data'] as $order)
        {
            $fInfo = $order->FinancialTotalInfo;
            $nestedData = [];
            $nestedData['id']               = $order->id;
            $nestedData['customer']         = $order->customer;
            $nestedData['deliver_note']     = $order->deliver_note;
            $nestedData['number']           = $order->number;
            $nestedData['number2']           = $order->number2;
            $nestedData['salesRep']          = $order->salesperson != null?
                                                $order->salesperson->firstname.' '.$order->salesperson->lastname:'';;
            $nestedData['clientname']       = $order->CName;
            $nestedData['companyname']      = $order->CPName;
            $nestedData['date']             = $order->date;
            $nestedData['total']            = $order->total_info['adjust_price'];
            $nestedData['rSub']             = $fInfo['rSubTotal'];
            $nestedData['rTax']             = $fInfo['rTax'];
            $nestedData['pDiscount']        = $order->rPDiscount;
            $nestedData['salesEmail']       = $order->SalesEmail;
            $nestedData['items']            = $order->getFulfilledItems();
            $data[] = $nestedData;
        }
        $json_data['data'] = $data;
        return response()->json($json_data);
    }

    public function _sendSalesEmail(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $invoice['sign_name'] = $invoice->sign_name;
        $invoice['sign_date'] = $invoice->sign_date;
        $this->generatePdf($invoice,'pdfTemplate.fulfilled_invoice');
        Mail::to($invoice->SalesEmail)->send(new SignSalesPerson($invoice));
        if($invoice->customer != null)
        {
            if($invoice->customer->secondaryc_email != null)
            {
                Mail::to($invoice->customer->secondaryc_email)->send(new SignSalesPerson($invoice));
            }
        }
        File::delete(public_path().'/storage/'.$invoice->number.'/invoice.pdf');
        File::delete(public_path().'/storage/'.$invoice->number.'/mail.pdf');
        return 1;
    }

    public function _set_d_status(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->delivery_status = $request->status;
        //$invoice->status = Config::get('constants.order.archived');
        $invoice->save();
        return 1;
    }

    public function Panel($id)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $data['invoice'] = $invoice;
        $data['isDelivered'] = $invoice->status == Config::get('constants.order.delivered');
        $data['fInfo'] = $invoice->FinancialTotalInfo;
        $data['dOptions'] = DeliveryStatus::where('id','!=',1)->get();
        $data['owedInvoices'] = $invoice->customer != null?$invoice->customer->FinacialInfo['myInvoices']:[];
        foreach($data['owedInvoices'] as $key => $invoice)
        {
            if($invoice['id'] == $id)
            {
                unset($data['owedInvoices'][$key]);
            }
        }
        return view('sign.panel',$data);
    }
    public function _collect_money(Request $request)
    {
        $order = InvoiceNew::find($request->id);
        /**
         * if There is Image Save it
         */
        $filename = null;
        if($request->signImage != null)
        {
            $imagedata = base64_decode($request->signImage);
            $filename = uniqid().'.png';

            //Location to where you want to created sign image
            $file_name = 'dSigns/'.$filename;

            if(!Storage::disk('public')->put($file_name, $imagedata))
            {
                return false;
            }
        }
        if($request->amountSubTotal > 0)
        {
            $log = new InvoicePaymentLog([  'amount' => $request->amountSubTotal,
                                            'type' => 2,
                                            'allowed' => 0,
                                            'user_id' => auth()->user()->id,
                                            'sign_filename' => $filename,
                                            'd_personame' => $request->dPersoname,
                                            'cash_serial' => $request->cash_serial]);
            $log->updated_at = $request->cDate.' '.date('H:i:s');
            $order->PaymentLog()->save($log);
        }
        if($request->amountTax > 0)
        {
            $log = new InvoicePaymentLog([ 'amount' => $request->amountTax,
                                       'type' => 1,
                                       'allowed' => 0,
                                       'user_id' => auth()->user()->id,
                                       'sign_filename' => $filename,
                                       'd_personame' => $request->dPersoname,
                                       'cash_serial' => $request->cash_serial]);
            $log->updated_at = $request->cDate.' '.date('H:i:s');
            $order->PaymentLog()->save($log);
        }
        //Saving Client
        $clientData = $request->clientData;
        if( $clientData != null)
        {
            $clientRequest = new Request;
            $clientRequest->id = $request->id;
            $clientRequest->img_data = $clientData['img_data'];
            $clientRequest->sign_date = $clientData['sign_date'];
            $clientRequest->sign_name = $clientData['sign_name'];
            $this->_save_sign($clientRequest);
        }
        return $order->id;
    }
    public function PaymentPanel($id)
    {
        $invoice = InvoiceNew::find($id);

        $ourdetail = OurDetail::all()->first();
        $invoice['company_detail']  = $ourdetail;
        $data['invoice'] = $invoice;
        $data['fInfo'] = $invoice->FinancialTotalInfo;
        $data['dOptions'] = DeliveryStatus::where('id','!=',1)->get();
        return view('sign.payment_panel',$data);
    }
    public function _deletePayment($id)
    {
        InvoicePaymentLog::find($id)->delete();
        return 1;
    }
    public function _save_deliver_note(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->deliver_note = $request->note;
        $invoice->save();
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
                            if($fgItem->qtyonhand > 0)
                                $fgItem->qtyonhand -= 1;
                            $fgItem->status = 3;
                        }
                    }
                    $fgItem->save();
                }
            }

            $invoice->sign_name = $request->sign_name;
            $invoice->sign_date = date('Y-m-d H:i:s',strtotime($request->sign_date.' '.date('H:i:s')));
            $invoice->number2 = explode('-',$invoice->number2)[1];
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
            // //set as delivered
            // $req = new Request;
            // $req->id = $request->id;
            // $req->status = 4;
            // $this->setOrderStatus($req);

            $this->setDeliveryStatus($invoice->id,2);
            //sending Email
            //get sales person name
            $ourdetail = OurDetail::all()->first();
            $invoice['company_detail']  = $ourdetail;
            $invoice['sign_name'] = $invoice->sign_name;
            $invoice['sign_date'] = $invoice->sign_date;
            $this->generatePdf($invoice,'pdfTemplate.fulfilled_invoice');
            Mail::to($invoice->SalesEmail)->send(new ReportOrderDelivery($invoice));
            File::delete(public_path().'/storage/'.$invoice->number.'/invoice.pdf');
            File::delete(public_path().'/storage/'.$invoice->number.'/mail.pdf');
            return '1';
        });
        return $flag;
    }

    public function pVerificationHome(Request $request)
    {
        //echo date('Y-m-d', strtotime('2020-03-16'. ' + 21 days'));exit;
        /**
         * 3.24
         * Load Deliverd Invoices
         * status:4
         */
        $invoices = InvoiceNew::whereIn('status',[4,5,6,7])->get();
        $cData = [];
        foreach($invoices as $invoice)
        {
            $item = [];
            $item['id'] = $invoice->id;
            $item['number'] = $invoice->number;

            $item['title'] = '';
            $item['start'] = $invoice->PayDate;
            $isPast = $item['start'] < date('Y-m-d')?1:0;

            if($isPast == 1)
            {
                $item['backgroundColor'] = '#d73925';
                $item['borderColor'] = '#d73925';
            }
            else
            {
                $item['backgroundColor'] = '#00c0ef';
                $item['borderColor'] = '#00c0ef';
            }
            $item['isContact'] = $invoice->ContactInfo != null?1:0;
            $fInfo = $invoice->FinancialTotalInfo;
            $item['title'] .= 'ST:$'.$fInfo['rSubTotal'].',Tax:$'.$fInfo['rTax'];
            if(!$fInfo['completed'])
                $cData[] = $item;
        }
        $data['cData'] = $cData;
        $data['viewUrl'] = 'payment_view/';
        $data['collectionUrl'] = '../../order_fulfilled/payment/';

        $data['signFileUrl'] = asset('storage/dSigns/');
        return view('sign/p_verification/home',$data);
    }
    public function _getPVerifications(Request $request)
    {
        $p = $request->p;
        $d = $request->d;
        //handle option
        switch($p)
        {
            //Awaiting Verification
            case '0':
                $cond = InvoiceNew::has('PaymentLog','>=',1)
                                    // ->whereHas('PaymentLog',function($query) {
                                    //     $query->where('allowed',0);
                                    // })
                                    ->leftjoin('terms as t','invoices_new.term_id','=','t.term_id')
                                    ->whereRaw('DATE_ADD(invoices_new.delivered,INTERVAL IFNULL(t.days,0) DAY) <= ?',
                                    [date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$d.' days'))]);
            break;
            //Awaiting Payment
            case '1':
                $cond = InvoiceNew::has('PaymentLog','==',0)
                                  ->leftjoin('terms as t','invoices_new.term_id','=','t.term_id')
                                  ->whereRaw('DATE_ADD(invoices_new.delivered,INTERVAL IFNULL(t.days,0) DAY) <= ?',
                                    [date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$d.' days'))]);
            break;
            //Paid
            case '2':
                $cond = InvoiceNew::where('paid','!=',null)
                                    ->whereRaw('paid <= ?',
                                    [date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$d.' days'))]);
            break;
        }
        $cond = $cond->whereIn('status',[4,5,6,7]);
        $result = [];
        foreach($cond->get() as $key => $order)
        {
            $item = [];
            $fInfo = $order->FinancialTotalInfo;
            $item['id']         = $order->id;
            $item['no']         = $key + 1;
            $item['number']     = $order->number;
            $item['number2']     = $order->number2;
            $item['clientname'] = $order->CName;
            $item['total']      = $fInfo['oTotal'];
            $item['rSubTotal']  = $fInfo['rSubTotal'];
            $item['rTax']       = $fInfo['rTax'];
            $item['date']       = $order->date;
            $item['payDate']    = $order->DTermday;
            $item['dDate']      = $order->delivered;
            $item['termLabel']  = $order->TermLabel;
            $item['paidDate']   = $order->paid;
            //prepare items
            $item['logs'] = ['allowed' => [],'unallowed' => []];
            foreach($fInfo['logs'] as $key => $items)
            {
                foreach($items as $val)
                {
                    $val['amount'] = '$'.$val['amount'];
                    if($val['allowed'] == 1)
                    {
                        $item['logs']['allowed'][] = $val;
                    }
                    if($val['allowed'] == 0)
                    {
                        $item['logs']['unallowed'][] = $val;
                    }
                }
            }
            $result[] = $item;
        }
        return $result;
        // return array(
		// 	"draw"			=> intval($request->input('draw')),
		// 	"recordsTotal"	=> intval($totalData),
		// 	"recordsFiltered" => intval($totalFiltered),
		// 	"data"			=> $result
		// );

    }
    public function _verifyPayment($invoice_id,$p_id,$amount)
    {
        $p = InvoicePaymentLog::find($p_id);
        $p->amount = $amount;
        $p->allowed = 1;
        $p->save();
        $this->archiveOrder($invoice_id);
    }
}
