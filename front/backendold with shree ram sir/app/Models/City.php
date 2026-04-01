<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    protected $table = 'tbl_city';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'name', 
            'status',
            'country_id',
            'state_id',
            'is_deleted'
        ];
    }

   

    
}
