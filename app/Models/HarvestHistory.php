<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\CommonFunction;

class HarvestHistory extends Model
{
    //
    protected $table = "harvest_history";
    use CommonFunction;



    public function get_history($date_range,$mode)
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

        $que = $this->select('harvest_history.*','harvests.harvest_batch_id')
             ->leftjoin('harvests','harvest_id','=','harvests.id');
             
        $field_name = "created_at";
        switch($mode)
        {
            case '0':
            $field_name = "created_at";
            break;
            case '1':
            $field_name = "dynamics";
            $que->where('dry','=',null);
            break;
            
            case '2':
            $field_name = "dry";
            $que->where('curning','=',null);
            break;
            
            case '3':
            $field_name = "curning";
            $que->where('holding','=',null);
            break;

            
            case '4':
            $field_name = "holding";
            $que->where('fg','=',null);
            break;
            
            case '5':
            $field_name = "fg";
            break;
        }
        return $que->whereRaw('DATE(harvest_history.'.$field_name.') >= ?', [$date_range['start_date']])
                ->whereRaw('DATE(harvest_history.'.$field_name.') <= ?', [$date_range['end_date']])
                ->orderby('harvest_history.'.$field_name,'desc')
            ->get();
    }
}
