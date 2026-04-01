<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Roles;

class Status extends Model
{
    protected $table = 'tbl_statuses';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'status_id', 
            'status_name',
            'status',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by'
        ];
    }
    
     public function roless()
     {
        return $this->hasOne(Roles::class,'roles_id','role_id');
     }

   

    
}
