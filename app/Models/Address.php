<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    protected $table = 'tbl_address';
    protected $primaryKey = 'id';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'address_id', 
            'employee_id', 
            'address',
            'city', 
            'state', 
            'country',
            'pincode',
            'status',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ];
    }

   

    
}