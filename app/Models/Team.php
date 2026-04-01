<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Employee;
use App\Models\Buisness;
class Team extends Model
{
    protected $table = 'tbl_team';
    public function __construct(){
        parent::__construct();
        $this->setFillable();
    }

    public function setFillable(){
        $this->fillable = [
            'id',
            'team_id',
            'buisness_id',
            'team_name',
            'manager_id',
            'teamlead_id',
            'teammember_id',
            'status'
        ];
    }
       public function bussiness(){
          return $this->hasOne(Buisness::class,'buisness_id','buisness_id');
      }
       public function manager(){
          return $this->hasOne(Employee::class,'employee_id','manager_id');
      }
       public function team_lead(){
          return $this->hasOne(Employee::class,'employee_id','teamlead_id');
      }
     public function members()
     {
          return $this->hasMany(Employee::class, 'employee_id', 'teammember_id');
     }
 
}
