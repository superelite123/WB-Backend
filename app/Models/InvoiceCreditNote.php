<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\HasManyRelation;
use App\User;
class InvoiceCreditNote extends Model
{
    use HasManyRelation;
    protected $fillable = [ 'invoice_id', 'no', 'customer_id', 'total_price','detail',
                            'original_total','archive','archived_at','reason_id','approved_at','approved_by'];
    //
    public function rItems()
    {
        return $this->hasMany(InvoiceCreditNoteItem::class,'parent_id');
    }
    public function rInvoice()
    {
        return $this->belongsTo(InvoiceNew::class,'invoice_id');
    }
    public function rReason()
    {
        return $this->belongsTo(InvoiceCreditNoteReason::class,'reason_id');
    }
    public function rApprover()
    {
        return $this->belongsTo(User::class,'approved_by');
    }
    public function getApproverAttribute()
    {
        $approver = $this->rApprover;
        return $approver != null ? $approver->name : '';
    }
    public function getLReasonAttribute()
    {
        return $this->rReason != null?$this->rReason->name : '';
    }
}
