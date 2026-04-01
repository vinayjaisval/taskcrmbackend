<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    protected $table = 'tbl_lead';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'name', 
            'assignee',
            'status',
            'category',
            'dedline',
            'is_deleted',
            'remarks',
            'added_by'
        ];
    }

   

    
}
