<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Login;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Address;

class LoginController extends Controller{
  public function index(){
    $this->login();
  }

  // === Login management --------
  public function login(Request $request){
  
    $login = Login::WHERE('is_deleted', '0')
            ->WHERE('userid', $request->userid)
            ->WHERE('status', '!=', '0')
            ->WHERE('password', md5($request->user_pass))
            ->first();
    if($login){
      $login = $login;
    } else {
      $login = "Invalid";
    }
    return response()->json($login); 
  }
  
   public function loginlatest(Request $request){
    
    $login = Employee::join('tbl_address', 'tbl_employees.employee_id', '=', 'tbl_address.employee_id')
              ->join('tbl_role', 'tbl_role.roles_id', '=', 'tbl_employees.role_id')
              //->join('tbl_buisness', 'tbl_buisness.buisness_id', '=', 'tbl_employees.buisness_id')
              //->join('tbl_users', 'tbl_users.id', '=', 'tbl_employees.team_id')
              ->select(
                    'tbl_employees.id',
                    'tbl_employees.employee_id',
                    'tbl_employees.photo',
                    'tbl_employees.first_name',
                    'tbl_employees.last_name',
                    'tbl_employees.dateofbirth',
                    'tbl_employees.gender',
                    'tbl_employees.contactno',
                    'tbl_employees.email',
                    'tbl_employees.password',
                    'tbl_employees.email',
                    'tbl_employees.password',
                    'tbl_employees.role_id',
                    //'tbl_employees.buisness_id',
                    'tbl_employees.team_id',
                    'tbl_employees.skills_id',
                    'tbl_address.country',
                    'tbl_address.address',
                    'tbl_address.city',
                    'tbl_address.state',
                    'tbl_address.pincode',
                    'tbl_role.roles_name',
                   // 'tbl_buisness.buisness_name',
                    //'tbl_users.name as teamname',
                    'tbl_employees.status'
                )
            ->WHERE('tbl_employees.email', $request->email)
            ->WHERE('tbl_employees.status', '=', '1')
            ->WHERE('tbl_employees.password', md5($request->password))
            ->first();
    if($login){
      $login = $login;
    } else {
      $login = "Invalid";
    }
    return response()->json($login); 
  }

  
}
