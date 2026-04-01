<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
{
    protected $table = 'tbl_role';
    protected $primaryKey = 'id';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'roles_id', 
            'roles_name', 
            'status',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ];
    }

   

    
}