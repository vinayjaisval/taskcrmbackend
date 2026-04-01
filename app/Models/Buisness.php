<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Buisness extends Model
{
    protected $table = 'tbl_buisness';
    protected $primaryKey = 'id';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'buisness_id', 
            'buisness_name', 
            'buisness_logo',
            'buisness_email',
            'buisness_contact', 
            'buisness_description', 
            'buisness_address', 
            'buisness_url', 
            'status',
            'buisness_email',
        ];
    }

   

    
}
