<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingDetail extends Model
{
    //
    protected $table = "shippingdetails";
    protected $fillable = ['trackingid','invoice_id','reference',
                           'shipping_carrier','shipment_date','actual_date',
                           'expected_date'];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class,'id','invoice_id');
    }

    public function carrier()
    {
        return $this->hasOne(Carrier::class,'carrierid','shipping_carrier');
    }
}
