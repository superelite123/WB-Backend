<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $primaryKey = 'client_id';
    protected $table = 'customers';

    public function Term()
    {
        return $this->belongsTo(Term::class,'terms','term_id');
    }

    
    public function basePrice()
    {
        return $this->hasMany(PriceMatrix::class,'customer_id');
    }
}
