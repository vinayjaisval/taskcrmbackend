<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UniLogin extends Model
{
    protected $table = 'tbl_uni_users';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'name', 
            'userid', 
            'password', 
            'email', 
            'phone',
            'password',
            'api_key',
            'user_img',
            'is_deleted'
        ];
    }

   

    
}
