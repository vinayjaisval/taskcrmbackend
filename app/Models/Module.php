<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Buisness;
use App\Models\Project;
class Module extends Model
{
    protected $table = 'tbl_module';
    protected $primaryKey = 'id';

    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'module_id', 
            'project_id', 
            'module_name',
            'start_date',
            'end_date', 
            'working hrs', 
            'module_weight',
            'description',
            'status',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ];
    }
    public function bussiness(){
      return $this->hasOne(Buisness::class,'buisness_id','buisness_id');
  }
    public function projectt(){
      return $this->hasOne(Project::class,'project_id','project_id');
  }

   

    
}
