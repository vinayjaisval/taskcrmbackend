<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Punch extends Model
{
    protected $table = 'tbl_punch';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'userid', 
            'latitude',
            'longitude',
            'remarks',
            'created_at',
            'updated_at'
        ];
    }

   

    
}
