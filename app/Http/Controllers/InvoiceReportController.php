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
use App\Models\License;
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
use App\Helper\CommonFunction;
use Config;

class InvoiceReportController extends OBaseController
{
    use CommonFunction;
    //
    public function index()
    {
        $data = [];
        $data['lTypes'] = License::all();
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 331 days')),
            'end_date' => Date('m/d/Y'),
        ]);
        return view('order.i_report',$data);
    }
    public function get_list(Request $request)
    {
        $request->status = [3,4];
        $date_range = $request->date_range;
        if($date_range == null)
        {
            $date_range['start_date'] = date('m/d/Y', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->convertDateRangeFormat($date_range);
        }
        $bCond = InvoiceNew::whereRaw('DATE(date) >= ?', [$date_range['start_date']])
                            ->whereRaw('DATE(date) <= ?', [$date_range['end_date']])
                            ->whereIn('status',$request->status);;
        $orderingColumn = $request->input('order.0.column');
        $dir = $request->input('order.0.dir');
        switch($orderingColumn)
        {
            case '2':
                $bCond = $bCond->orderBy('number',$dir);
            break;
            case '3':
                $bCond = $bCond->with(['customer' => function($query) use ($dir){
                    $query->orderBy('clientname',$dir);
                }]);
                break;
            case '4':
                $bCond = $bCond->orderBy('total',$dir);
                break;
            case '5':
                $bCond = $bCond->orderBy('date',$dir);
                break;
            default:
                $bCond = $bCond->orderBy('date','desc');
                $bCond = $bCond->orderBy('number','desc');
        }
        $totalData = $bCond->count();
        $limit = $request->input('length') != -1?$request->input('length'):$totalData;
		$start = $request->input('start');
        $totalFiltered = $bCond->count();
        $l_type = $request->l_type;
        if($l_type != '0')
        {
            $bCond = $bCond->WhereHas('customer',function($query) use ($l_type){
                $query->where('licensetype','=',$l_type);
            });
        }
        if(empty($request->input('search.value'))){
            $totalFiltered  = $bCond->count();
            $orders = $bCond->offset($start)->limit($limit)->get();
        }
        else
        {
            $search = $request->input('search.value');
            $cond = $bCond->where('number','like',"%{$search}%")
                    ->orWhereHas('customer',function($query) use ($search){
                        $query->where('clientname','like',"%{$search}%");
                    })
                    ->orWhereHas('distuributor',function($query) use ($search){
                        $query->where('companyname','like',"%{$search}%");
                    })
                    ->orWhere('total','like',"%{$search}%")
                    ->orWhere('date','like',"%{$search}%");
            $totalFiltered  = $cond->count();
            $limit = $request->input('length') != -1?$request->input('length'):$totalFiltered;
            $orders      = $cond->offset($start)->limit($limit)->get();
        }

        $response = array(
			"draw"			=> intval($request->input('draw')),
			"recordsTotal"	=> intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
            'data'        => []
		);
        foreach($orders as $order)
        {
            $nestedData = [];
            $fInfo = $order->FinancialTotalInfo;
            $nestedData['id']               = $order->id;
            $nestedData['number']           = $order->number;
            $nestedData['clientname']       = $order->CName;
            $nestedData['total_info']       = $order->TotalInfoForExport;
            $nestedData['rSubTotal']        = $fInfo['rSubTotal'];
            $nestedData['rTax']             = $fInfo['rTax'];
            $nestedData['date']             = $order->date;
            $customer = Customer::find($order->customer['client_id']);
            $nestedData['items']            = $order->getFulfilledItems();
            $response['data'][] = $nestedData;
        }
        return response()->json($response);
    }
}
