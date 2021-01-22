<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerType;
use App\Models\State;
use App\Models\Nda;
use Mail;
use App\Mail\NDASender;
use App\Models\NDALog;
//LIB
use Storage;
use PDF;
use File;
use HTML;
use JavaScript;
use App\Helper\CommonFunction;
class NdaManagementController extends Controller
{
    use CommonFunction;
    //
    public function home()
    {
        $data = ['ndas' => [],'logs' => []];
        $data['ndas'] = Nda::all();
        JavaScript::put([
            'start_date' => date('m/d/Y', strtotime('today - 31 days')),
            'end_date' => Date('m/d/Y'),
        ]);
        return view('nda_m.home',$data);
    }
    public function getNdaLogs(Request $request)
    {
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
        $logs = NDALog::whereRaw('DATE(created_at) >= ?', [$date_range['start_date']])
                      ->whereRaw('DATE(created_at) <= ?', [$date_range['end_date']])->get();

        $response = [];
        foreach($logs as $key => $log)
        {
            $temp = [];
            $temp['no']         = $key + 1;
            $temp['name']       = $log->rUser->name;
            $temp['email']      = $log->rUser->email;
            $temp['ndaEmail']   = $log->rNda->email;
            $temp['date']       = $log->created_at->format('Y-m-d H:i');

            $response[] = $temp;
        }
        return $response;
    }
    public function view($id)
    {
        $nda = Nda::find($id);
        $id_file = null;
        if (Storage::disk('id_uploads')->exists($nda->id_file))
        {
            $id_file = 'data:image/png;base64,'.base64_encode(
                        decrypt(Storage::disk('id_uploads')->get($nda->id_file))
                        );
        }
        return view('nda_m.view',['nda' => $nda,'id_file' => $id_file]);
    }

    public function deleteID($id)
    {
        //Storage::disk('id_uploads')->delete(Storage::disk('id_uploads')->allFiles())
        if(!Storage::disk('id_uploads')->delete(Nda::find($id)->id_file))
        {
            return 0;
        }
        return 1;
    }
}
