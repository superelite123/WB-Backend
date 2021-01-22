<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Helper\HasManyRelation;
class InvImportHistory extends Model
{
    use HasManyRelation;
    protected $fillable = ['h_id', 'strain_id', 'category_id', 'importer',
                           'approver', 'status', 'imported_at', 'approved_at', 'fgasset_id', 'i_type'];
    public function rHarvest()
    {
        return $this->belongsTo(Harvest::class, 'h_id');
    }
    public function rStrain()
    {
        return $this->belongsTo(Strainame::class,'strain_id');
    }
    public function rCategory()
    {
        return $this->belongsTo(Producttype::class,'category_id');
    }
    public function rImporter()
    {
        return $this->belongsTo(User::class, 'importer');
    }
    public function rApprover()
    {
        return $this->belongsTo(User::class, 'approver');
    }
    public function rItems()
    {
        return $this->hasMany(InvImportHistoryItem::class, 'p_id');
    }
    public function getStrainLabelAttribute()
    {
        return $this->rStrain != null?$this->rStrain->strain:'';
    }
    public function getCategoryLabelAttribute()
    {
        return $this->rCategory != null?$this->rCategory->producttype:'';
    }
}
