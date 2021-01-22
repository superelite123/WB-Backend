<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarvestDynamics extends Model
{
    //
    protected $table = "harvestdynamics";
    protected $fillable = ['parent_id','unit_weight','total_weight','cultivator_company_id','strain_id',
                           'cultivator_license_id'];

    public function Strain()
    {
        return $this->belongsTo(Strainame::class,'strain_id');
    }

    public function License()
    {
        return $this->belongsTo(Cultivator::class,'cultivator_license_id');
    }

    public function Room()
    {
        return $this->belongsTo(LocationArea::class,'cultivator_company_id');
    }

    public function UnitOfWeight()
    {
        return $this->belongsTo(Unit::class,'unit_weight');
    }
}
