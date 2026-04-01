<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Project;
use App\Models\Module;
class Tasktodo extends Model
{
    protected $table = 'tbl_task';
    protected $primaryKey = 'id';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'task_id', 
            'module_id', 
            'project_id',
            'start_date',
            'end_date', 
            'working hrs', 
            'description',
            'status',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ];
    }
    
 public function module(){
      return $this->hasMany(Module::class,'module_id','module_id');
  }
    public function projectt(){
      return $this->hasMany(Project::class,'project_id','project_id');
  }

   

    
}
