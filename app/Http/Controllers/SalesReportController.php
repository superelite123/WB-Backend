<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\InvoiceNew;
use App\Models\InventoryRestockLog;
use DB;
class SalesReportController extends Controller
{
    public function index(Request $request)
    {
      $date[0] = $request->sdate != null ? $request->sdate : date('Y-m-01');
      $date[1] = $request->edate != null ? $request->edate : date('Y-m-t');
      if($date[1] < $date[0])
      {
        $date[1] = $date[0];
      }
      
      $data['mode'] = $request->mode != null ? $request->mode : 1;
      $stat = null;
      $title = "";
      $numberPrifix = '$';
      switch($data['mode'])
      {
        case 1:
          $stat = $this->getDataByDate($date);
          $title = "Monthly Sales";
          break;
        case 2:
          $stat = $this->getDataBySalesRep($date);
          $title = "Top Sales Rep";
          break;
        case 3:
          $stat = $this->getDataByCustomer($date);
          $title = "Top Customer";
          break;
        case 4:
          $stat = $this->getDataByRInventory($date);
          $title = "Top Returned Inventory";
          $numberPrifix = '';
          break;
        case 5:
          $stat = $this->getDataByRInventoryBySalesRep($date);
          $title = "Most Returned Item by Sales Rep";
          $numberPrifix = '';
          break;
        default:
          $stat = $this->getDataByDate($date);
          $title = "Monthly Sales";
          break;
      }
      $data['chartData'] = "{
          \"chart\": {
            \"caption\": \"".$title."\",
            \"showValues\":\"1\",
            \"showPercentInTooltip\" : \"0\",
            \"numberPrefix\" : \"".$numberPrifix."\",
            \"enableMultiSlicing\":\"1\",
            \"theme\": \"fusion\"
          },
          \"data\": ".$stat."}";
      $data['date'] = $date;
      return view('sales_report.index',$data);
    }

    private function getDataByDate($date)
    {
      //concat(c.firstname,c.lastname)
      $result = InvoiceNew::select(
                      DB::raw(" date as label"),
                      DB::raw("sum(total) as value"))
                      ->leftjoin('contactperson as c','salesperson_id','=','c.contact_id')
                      ->whereRaw('DATE(date) >= ?', [$date[0]])
                      ->whereRaw('DATE(date) <= ?', [$date[1]])
                      ->groupBy('date')
                      ->get()->toarray();
      return json_encode($result);
    }
    private function getDataBySalesRep($date)
    {
      //concat(c.firstname,c.lastname)
      $result = InvoiceNew::select(
                      DB::raw(" concat(c.firstname,c.lastname) as label"),
                      DB::raw("sum(total) as value"))
                      ->leftjoin('contactperson as c','salesperson_id','=','c.contact_id')
                      ->whereRaw('DATE(date) >= ?', [$date[0]])
                      ->whereRaw('DATE(date) <= ?', [$date[1]])
                      ->groupBy('salesperson_id')
                      ->orderBy('value')
                      ->get()->toarray();
      return json_encode($result);
    }
    private function getDataByCustomer($date)
    {
      //concat(c.firstname,c.lastname)
      $result = InvoiceNew::select(
                      DB::raw(" clientname as label"),
                      DB::raw("sum(total) as value"))
                      ->leftjoin('customers as c','customer_id','=','c.client_id')
                      ->whereRaw('DATE(date) >= ?', [$date[0]])
                      ->whereRaw('DATE(date) <= ?', [$date[1]])
                      ->groupBy('customer_id')
                      ->orderBy('value')
                      ->get()->toarray();
      return json_encode($result);
    }
    private function getDataByRInventory($date)
    {
      $result = InventoryRestockLog::select(
                DB::raw(" concat(concat(s.strain,','),p.producttype) as label "),
                DB::raw("count(*) as value"))
                ->leftjoin('fginventory as f','inventory_restock_log.fgasset_id','=','f.fgasset_id')
                ->leftjoin('strainname as s','s.itemname_id','=','f.strainname')
                ->leftjoin('productcategory as p','p.producttype_id','=','f.asset_type_id')
                ->whereRaw('DATE(inventory_restock_log.created_at) >= ?', [$date[0]])
                ->whereRaw('DATE(inventory_restock_log.created_at) <= ?', [$date[1]])
                ->groupBy('inventory_restock_log.fgasset_id')
                ->get()->toarray();
      return json_encode($result);
    }
    private function getDataByRInventoryBySalesRep($date)
    {
      $result = InventoryRestockLog::select(
                DB::raw(" concat(c.firstname,c.lastname) as label "),
                DB::raw("count(*) as value"))
                ->leftjoin('invoices_new as i','order_id','=','i.id')
                ->leftjoin('contactperson as c','i.salesperson_id','=','c.contact_id')
                ->whereRaw('DATE(inventory_restock_log.created_at) >= ?', [$date[0]])
                ->whereRaw('DATE(inventory_restock_log.created_at) <= ?', [$date[1]])
                ->groupBy('salesperson_id')
                ->get()->toarray();
      return json_encode($result);
    }
}
