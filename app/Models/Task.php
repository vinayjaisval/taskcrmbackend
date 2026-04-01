<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Project;
use App\Models\Module;
class TaskModules extends Model
{
    protected $table = 'module';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id', 
            'project_id', 
            'module_name',
            'start_date',
            'end_date',
            'created_at',
            'updated_at'
        ];
    }
    
    public function module(){
      return $this->hasOne(Module::class,'module_id','module_id');
  }
    public function projectt(){
      return $this->hasOne(Project::class,'project_id','project_id');
  }

   

    
}
