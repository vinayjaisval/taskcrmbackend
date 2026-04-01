<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallerDesk extends Model
{
    protected $table = 'tbl_callerdesk';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'sourcenumber',
            'destinationnumber',
            'status'
        ];
    }

   

    
}
