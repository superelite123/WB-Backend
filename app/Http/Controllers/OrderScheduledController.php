<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InvoiceNew;
use JavaScript;
use App\Helper\CommonFunction;
use App\Models\Delivery;
class OrderScheduledController extends Controller
{
    use CommonFunction;
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $dateRange = $request->date_range;
        if($dateRange == null)
        {
            $dateRange = [];
            //default date range
            $dateRange['start_date'] = date('Y-m-01');
            $dateRange['end_date'] = Date('Y-m-t');
        }
        else
        {
            if($dateRange == '1')
            {
                //get this week's monday and sunday
                $dateRange = [];
                //check if monday
                if(date('w') == 1){
                    $dateRange['start_date'] = date('Y-m-d');
                }
                else
                {
                    $dateRange['start_date'] = date('Y-m-d', strtotime('previous monday'));
                }
                $dateRange['end_date'] = date('Y-m-d',strtotime('next sunday'));
            }
            else
            {
                $dateRange = $this->convertDateRangeFormat($dateRange);
            }
        }

        $cond = InvoiceNew::whereRaw('DATE(delivery_time) >= ?', [$dateRange['start_date']])
                           ->whereRaw('DATE(delivery_time) <= ?', [$dateRange['end_date']]);
        $totalStat = [];
        $totalStat['sub_total']         = number_format($cond->sum('tbp'),2);
        $totalStat['discount_total']    = number_format($cond->sum('discount'),2);
        $totalStat['e_discount_total']  = number_format($cond->sum('e_discount'),2);
        $totalStat['tax_total']         = number_format($cond->sum('tax'),2);
        JavaScript::put([
            'start_date' => date('m/d/Y',strtotime($dateRange['start_date'])),
            'end_date' => date('m/d/Y',strtotime($dateRange['end_date'])),
        ]);
        return view('orderFulfilled.scheduled',[    'cData' => $this->fnGetScheduledDataByDateRange($dateRange),
                                                    'total_stat' => $totalStat,
                                                    'deliveries' => Delivery::all(),]);
    }
    public function _getCalendarRequest(Request $request)
    {

        return response()->json($this->fnGetScheduledDataByDateRange(['start_date' => $request->start,'end_date' => $request->end]));
    }
    public function fnGetScheduledDataByDateRange($dateRange)
    {
        $orders = InvoiceNew::whereRaw('DATE(delivery_time) >= ?', [$dateRange['start_date']])
                    ->whereRaw('DATE(delivery_time) <= ?', [$dateRange['end_date']])
                    ->get();
        $cData = [];
        foreach($orders as $order)
        {
            $item = [];
            $item['id']     = $order->id;
            $item['number'] = $order->number2;
            $item['numberSO'] = $order->number;
            $item['dDate']  = date('m/d/Y',strtotime($order->delivery_time));
            $item['deliveryer'] = $order->rDevlieryer != null?$order->rDevlieryer->username:'Empty';
            $item['deliveryerID'] = $order->rDevlieryer != null?$order->rDevlieryer->id:-1;
            $item['time'] = date('h:i a',strtotime($order->delivery_time));
            $item['cName']  = $order->cName;
            $item['amount'] = $order->total_info['adjust_price'];
            $item['title1'] = 'Invoice: '.$item['number'];
            $item['title2'] = 'Store: '.$item['cName'];
            $item['title3'] = 'Total: $'.$item['amount'];
            $item['title4'] = 'Time: '.$item['time'];
            $item['title5'] = 'Sales Order: '.$item['numberSO'];
            $item['isDelivered'] = $order->status == 4?1:0;
            if($item['isDelivered'] == 0)
            {
            $item['backgroundColor'] = '#d73925';
            }
            else
            {
            $item['backgroundColor'] = 'MediumSeaGreen';
            }
            $item['borderColor'] = $item['backgroundColor'];
            $item['start'] = $item['dDate'];
            $cData[] = $item;
        }
        return $cData;
    }
    public function changeDate(Request $request)
    {
        $date = date("Y-m-d H:i:s",strtotime($request->date));
        $invoice = InvoiceNew::find($request->id);
        $invoice->delivery_time = $date;
        $invoice->deliveryer = $request->deliveryer;
        $invoice->save();
        return response()->json(['success' => 1]);
    }
    public function deleteDate(Request $request)
    {
        $invoice = InvoiceNew::find($request->id);
        $invoice->delivery_time = null;
        $invoice->deliveryer = null;
        $invoice->save();
        return response()->json(['success' => 1]);
    }
}
