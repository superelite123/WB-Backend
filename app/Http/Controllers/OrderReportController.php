<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderReportController extends OBaseController
{
    //construct
    public function __construct()
    {

    }

    public function home(Request $request)
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
