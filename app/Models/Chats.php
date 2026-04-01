<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chats extends Model
{
    protected $table = 'tbl_chat';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id',
            'memb_id',
            'chat_whome',
            'message',
            'group_id',
            'is_deleted',
            'created_at',
            'updated_at'
        ];
    }

   

    
}
