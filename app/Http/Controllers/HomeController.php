<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Harvest;
use App\Models\HarvestItem;
use App\Models\Strainame;
use App\Models\Cultivator;
use App\Models\Unit;
use App\Models\LocationArea;
use App\Models\Invoice;
use App\Models\ContactPerson;
use App\Models\Clocking;
use App\Models\ClockingDriver;
use JavaScript;
use DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {

    //controls defailt chart date
        $first_day_this_month = date('Y-m-t', strtotime('today - 31 days')); // hard-coded '01' for first day
        $last_day_this_month  = date('Y-m-t');

        $harvest = new Harvest;

        $data['plant_cnt'] =HarvestItem::select(DB::raw('count(*) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;

        $data['harvest_cnt'] = Harvest::select(DB::raw('count(*) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;
        $data['invoice_cnt'] = Invoice::select(DB::raw('count(*) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;

       $data['wetweight'] = Harvest::select(DB::raw('format(sum(total_weight),2) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;

        $data['wetweightlbs'] = Harvest::select(DB::raw('format(sum(total_weight * 0.00220462),2) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;

       $data['wetweightantlbs'] = Harvest::select(DB::raw('format(sum(total_weight *0.00220462 /5),2) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;

         $data['wetweightantoz'] = Harvest::select(DB::raw('format(sum(total_weight *0.002204 /5 *16),2) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;

        $data['wetweightantgr'] = Harvest::select(DB::raw('format(sum(total_weight *0.002204 /5 *453.592),2) as cnt'))
                              ->whereRaw('DATE(created_at) >= ?', [$first_day_this_month])
                              ->whereRaw('DATE(created_at) <= ?', [$last_day_this_month])
                              ->get()[0]->cnt;

        JavaScript::put([
            'start_date' => $first_day_this_month,
            'end_date' => $last_day_this_month,
        ]);

        return view('home',$data);
    }

    public function get_harvest_chart_data(Request $request)
    {
        $harvest = new Harvest;
        return $harvest->get_harvest_chart_data($request->s_date,$request->e_date);
    }

    public function get_strain_chart_data(Request $request)
    {
        $harvest =  new Harvest;
        return $harvest->get_strain_chart_data($request->s_date,$request->e_date);
    }

    public function clocking(Request $request)
    {
        $data['harvesters'] = ContactPerson::where('contacttype',5)->get();
        $data['clocking_harvesters']  = Clocking::with('user')->where('status',1)->get();
        $data['mode'] = $request->mode == null?1:$request->mode;
        JavaScript::put([
            'clocking_harvesters' => $data['clocking_harvesters'],
        ]);

        return view('clocking',$data);

    }

    public function clockingDistro(Request $request)
    {
        $data['drivers'] = ContactPerson::where('contacttype',12)->get();
        $data['clocking_drivers']     = ClockingDriver::with('user')->where('status',1)->get();
        $data['mode'] = $request->mode == null?1:$request->mode;
        JavaScript::put([
            'clocking_drivers'=> $data['clocking_drivers'],
        ]);

        return view('clocking_distro',$data);
    }/**
     * @
     */
    public function _set_lunch_in(Request $request)
    {
        $uid = $request->user_id;
        if($request->status == '1')
        {
            $clock = new ClockingDriver;
            $clock->user_id = $uid;
            $clock->start_time = date('Y-m-d H:i:s');
            $clock->status = 1;
            $clock->save();
        }
        else
        {
            $clock = ClockingDriver::where('user_id',$uid)->where('status',1)->first();
            $clock->status = 0;
            $clock->end_time = date('Y-m-d H:i:s');
            $clock->save();
        }

        return response()->json(['success' => 1]);
    }
    public function _set_clock_in(Request $request)
    {
        $uid = $request->user_id;
        if($request->status == '1')
        {
            $clock = new Clocking;
            $clock->user_id = $uid;
            $clock->start_time = date('Y-m-d H:i:s');
            $clock->status = 1;
            $clock->save();
        }
        else
        {
            $clock = Clocking::where('user_id',$uid)->where('status',1)->first();
            $clock->status = 0;
            $clock->end_time = date('Y-m-d H:i:s');
            $clock->save();
        }

        $data['clocking_harvesters'] = Clocking::with('user')->where('status',1)->get();

        return $data['clocking_harvesters'];
    }

    public function clocking_report()
    {
        $s_date = date('m/d/Y', strtotime('today - 31 days'));
        $e_date = date('m/d/Y');

        JavaScript::put([
            's_date' => $s_date,
            'e_date' => $e_date,
        ]);

        return view('clocking_report');
    }

    public function _get_clocking_data(Request $request)
    {
        $date_range = [];
        if($request->date_range == null)
        {
            $date_range['start_date'] = date('m/d/Y', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->change_date_format($request->date_range);
        }

        return response()->json(
            Clocking::with('user')
            ->where('status',0)
            ->whereRaw('DATE(end_time) >= ?', [$date_range['start_date']])
            ->whereRaw('DATE(end_time) <= ?', [$date_range['end_date']])
            ->orderBy('end_time','desc')
            ->get()
        );
    }
    public function clocking_lunch_report()
    {
        $s_date = date('m/d/Y', strtotime('today - 31 days'));
        $e_date = date('m/d/Y');
        JavaScript::put([
            's_date' => $s_date,
            'e_date' => $e_date,
            'isAdmin' => auth()->user()->can('Administration')?1:0,
        ]);
        
        return view('clocking_lunch_report');
    }
    public function _get_clocking_lunch_data(Request $request)
    {
        $date_range = [];
        if($request->date_range == null)
        {
            $date_range['start_date'] = date('m/d/Y', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->change_date_format($request->date_range);
        }

        return response()->json(
            ClockingDriver::with('user')
            ->where('status',0)
            ->whereRaw('DATE(start_time) >= ?', [$date_range['start_date']])
            ->whereRaw('DATE(end_time) <= ?', [$date_range['end_date']])
            ->orderBy('end_time','desc')
            ->get()
        );
    }
    public function deleteDistroLog(Request $request)
    {
        ClockingDriver::find($request->id)->delete();
        return 1;
    }
    private function change_date_format($date_range)
    {
        if($date_range == null)
        {
            return;
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

    public function _change_time_range(Request $request)
    {
        $row_id = $request->row_id;
        $s_time = $request->s_time;
        $e_time = $request->e_time;
        $clocking = Clocking::find($row_id);
        $clocking->start_time = explode(' ',$clocking->start_time)[0].' '.$s_time;
        $clocking->end_time = explode(' ',$clocking->end_time)[0].' '.$e_time;
        $clocking->save();

        return 1;
    }

    public function _get_clocking_chart_data(Request $request)
    {

        $date_range = [];
        if($request->date_range == null)
        {
            $date_range['start_date'] = date('m/d/Y', strtotime('today - 31 days'));
            $date_range['end_date']   = date('Y-m-d');
        }
        else
        {
            $date_range = $this->change_date_format($request->date_range);
        }

        $data = Clocking::with('user')
        ->where('status',0)
        ->whereRaw('DATE(end_time) >= ?', [$date_range['start_date']])
        ->whereRaw('DATE(end_time) <= ?', [$date_range['end_date']])
        ->get();

        $chart_data = [];
        foreach($data as $item)
        {
            if($item->user != null)
            {
                $chart_data['label'][]['label'] = $item->user->firstname.' '. $item->user->lastname.substr($item->start_time,0,10);
            }
            else
                $chart_data['label'][]['label'] = 'No Harvester';

            $chart_data['value'][]['value'] = $this->time_diff($item->start_time,$item->end_time);
        }
        return $chart_data;
    }

    private function time_diff($start,$end)
    {
        $datetime1 = new \DateTime($start);
        $datetime2 = new \DateTime($end);
        $interval = $datetime1->diff($datetime2);
        $a = ($interval->format('%h') + $interval->format('%i')/60.0);
        return $a;
        return $interval->format('%h')." Hours ".$interval->format('%i')." Minutes";
    }
}
