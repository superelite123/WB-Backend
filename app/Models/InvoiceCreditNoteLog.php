<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;

class InvoiceCreditNoteLog extends Model
{
    use HasManyRelation;
    protected $fillable = ['credit_note','invoice_id','amount'];
    //
    public function rCreditNote()
    {
        return $this->belongsTo(InvoiceCreditNote::class,'credit_note');
    }
    public function rInvoice()
    {
        return $this->belongsTo(InvoiceNew::class,'invoice_id');
    }
}
