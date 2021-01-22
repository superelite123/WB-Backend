<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class InvoiceItem extends Model
{
    //

    protected $table  ='invoice_items';

    protected $primay_key = 'id';

    protected $fillable = ['item_id','unit_price','qty','discount','tax','tax_note'];

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }
    
    public function attr()
    {
        return $this->belongsTo(InvoiceGood::class,'item_id');
    }
    
    // ->join('strainname','invoice_good.strainname','=','strainname.itemname_id')
    // ->join('productcategory','productcategory.producttype_id','=','invoice_good.asset_type_id')
    public function get_invoice_items($invoice_id)
    {
        $result = $this->select('invoice_items.*',DB::raw("CONCAT(CONCAT(invoice_good.metrc_tag,'-',CONCAT(invupccont.upc)),'-',invupccont.strain,' ',invupccont.type) AS description"),'invupccont.strain','invoice_good.coa','invoice_good.weight')
                    ->join('invoice_good','invoice_items.item_id','=','invoice_good.fgasset_id')
                    ->leftJoin('invupccont','invupccont.iteminv_id','=','invoice_good.upc_fk')
                    ->where('invoice_items.invoice_id',$invoice_id)
                    ->with('attr')
                    ->get();

        foreach($result as $item)
        {
            $item->sub_total = $item->qty * $item->unit_price;
            $item->less_discount = $item->sub_total - $item->discount;
            $item->adjust_price = $item->less_discount + $item->tax;
            
            $item->sub_total = number_format((float)$item->sub_total, 2, '.', '');
            $item->less_discount = number_format((float)$item->less_discount, 2, '.', '');
            $item->adjust_price = number_format((float)$item->adjust_price, 2, '.', '');
        }

        return $result;
    }
}
