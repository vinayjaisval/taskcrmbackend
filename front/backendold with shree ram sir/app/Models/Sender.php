<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sender extends Model
{
    protected $table = 'tbl_sender';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'name', 
            'entity_id',
            'is_deleted'
        ];
    }

   

    
}
