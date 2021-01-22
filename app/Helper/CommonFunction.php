<?php
namespace App\Helper;

trait CommonFunction{
    public function convertDateRangeFormat($date_range)
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
}