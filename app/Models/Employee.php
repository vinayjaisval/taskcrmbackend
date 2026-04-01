<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Roles;
class Employee extends Model
{
    protected $table = 'tbl_employees';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id',
            'employee_id',
            'photo',
            'first_name',
            'last_name',
            'dateofbirth',
            'gender',
            'contactno',
            'email',
            'password',
            'role_id',
            'buisness_id',
            'team_id',
            'skills_id',
            'name', 
            'status',
            'is_deleted'
        ];
    }
     public function roles(){
        return $this->hasMany(Roles::class,'roles_id','role_id');
    }
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'teams', 'id', 'teammember_id');
    }
   

    
}
