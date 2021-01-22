<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceCreditNoteItem extends Model
{
    protected $fillable = ['parent_id','strain','p_type','price','qty'];
    public  $timestamps = false;

    public function rStrain()
    {
        return $this->belongsTo(Strainame::class,'strain');
    }

    public function rPType()
    {
        return $this->belongsTo(Producttype::class,'p_type');
    }

    public function getLabelAttribute()
    {
        $strain = $this->rStrain;
        $pType  = $this->rPType;

        $label = $strain != null ? $strain->strain : '';
        $label .= $pType != null ? $pType->producttype : '';

        return $label;
    }
}
