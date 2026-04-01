<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Buisness;
use App\Models\Project;
use App\Models\Employee;
class Project extends Model
{
    protected $table = 'tbl_project';
    protected $primaryKey = 'id';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'project_id', 
            'project_name', 
            'start_date',
            'end_date',
            'total_time', 
            'buisness_id', 
            'manager_id',
            'teamleader_id',
            'document',
            'url',
            'details',
        ];
    }
  
  public function bussiness(){
      return $this->hasOne(Buisness::class,'buisness_id','buisness_id');
  }
  
   public function projectt(){
      return $this->hasOne(Project::class,'project_id','project_id');
  }
  
   public function managree(){
      return $this->hasOne(Employee::class,'employee_id','manager_id');
  }

   public function teamlead()
   {
      return $this->hasOne(Employee::class,'employee_id','teamleader_id');
   }
   

    
}
