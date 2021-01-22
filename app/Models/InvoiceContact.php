<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceContact extends Model
{
    //
    protected $attributes = [ 'contact_person' => 0, 'p_date' => 1,
                              'c_sub_toal' => '','c_tax' => '','note' => ''];
    protected $fillable = ['contact_person','p_date','c_sub_total','c_tax','note'];
    public function __construct(array $attributes = array())
    {
        $this->setRawAttributes(array(
        'contact_person' => '',
        'p_date' => date('Y-m-d'),
        'c_sub_total' => '',
        'c_tax' => '',
        'note' => ''
        ), true);
        parent::__construct($attributes);
    }
}
