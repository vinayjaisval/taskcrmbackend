<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\CallerDesk;
use App\Models\Employee;
use App\Models\Address;
use App\Models\TaskModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;

class EmployeeController extends Controller{
    
    public function add_employee(Request $request)
   {
       
              $request->validate
              ([
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'contactno' => 'required',
                    'email' => 'required',
                    // 'image' => 'required',
                ]);
            $phone_exists = Employee::WHERE('contactno', $request->contactno)
                    ->first();
            $email_exists = Employee::WHERE('email', $request->email)
                    ->first();
             if($phone_exists)
            {
              $result = "Phone No Already Exists. You can not save Employee!!!";
              $data['status'] = 401;
            }
            else if($email_exists)
            {
              $result = "Email Id Already Exists. You can not save Employee!!!";
              $data['status'] = 401;
            } 
            else 
            {
                
                  define('Em', 'skyemp');
                  define('Bs', 'address');
                  $emp_id = Em.substr(uniqid(), -3);
                  
                 if($request->file('image'))
                {
                  $file= $request->file('image');
                  $filename= date('YmdHi').$file->getClientOriginalName();
                  $file->move(public_path('uploads'), $filename);
                 
                  $employee = new Employee;
                  $employee->employee_id = $emp_id;
                  $employee->photo = $filename;
                  $employee->first_name = $request->first_name;
                  $employee->last_name = $request->last_name;
                  $employee->dateofbirth = $request->dateofbirth;
                  $employee->gender = $request->gender;
                  $employee->contactno = $request->contactno;
                  $employee->email = $request->email;
                  $employee->password  = md5($request->password);
                  $employee->buisness_id = $request->buisness_id;
                  $employee->role_id = $request->role_id;
                  $employee->team_id= $request->team_id;
                  if(is_array($request->skills))
                  {
                      $skills = implode(", ", $request->skills);
                  }else{
                      $skills = $request->skills;
                  }
          
                  $employee->skills_id  = $skills ;
                  $employee->created_at  = date('m/d/Y h:i:s a', time());
                  $employee->status ='1';
                  $employee->save();
                
                  $address = new Address;
                  $address->address_id = Bs.substr(uniqid(), -3);
                  $address->employee_id = $emp_id;
                  $address->address = $request->address;
                  $address->city = $request->city;
                  $address->state = $request->state;
                  $address->country = $request->country;
                  $address->pincode = $request->pincode;
                  $address->created_at  = date('m/d/Y h:i:s a', time());
                  $address->status ='1';
                  $address->save();
                  $result = "Data Store Successfully!!!";
                }
                
                else{
        
                  $employee = new Employee;
                  $employee->employee_id = $emp_id;
                  //$employee->photo = $filename;
                  $employee->first_name = $request->first_name;
                  $employee->last_name = $request->last_name;
                  $employee->dateofbirth = $request->dateofbirth;
                  $employee->gender = $request->gender;
                  $employee->contactno = $request->contactno;
                  $employee->email = $request->email;
                  $employee->password  = md5($request->password);
                  $employee->buisness_id = $request->buisness_id;
                  $employee->role_id = $request->role_id;
                  $employee->team_id= $request->team_id;
                   if(is_array($request->skills))
                  {
                      $skills = implode(", ", $request->skills);
                  }else{
                      $skills = $request->skills;
                  }
                  $employee->skills_id  = $skills ;
                  //$employee->skills_id  = implode(", ", $request->skills_id);
                  $employee->created_at  = date('m/d/Y h:i:s a', time());
                  $employee->status ='1';
                  $employee->save();
                
                  $address = new Address;
                  $address->address_id = Bs.substr(uniqid(), -3);
                  $address->employee_id = $emp_id;
                  $address->address = $request->address;
                  $address->city = $request->city;
                  $address->state = $request->state;
                  $address->country = $request->country;
                  $address->pincode = $request->pincode;
                  $address->created_at  = date('m/d/Y h:i:s a', time());
                  $address->status ='1';
                  $address->save();
                  $result = "Data Store Successfully!!!";
                
                }
                $data['status'] = 200;
        
            }
      
              $data['response'] = $result;
         
            return response()->json($data);
   }
   
    public function edit_employee($id)
   {
      $employeeData = Employee::join('tbl_address', 'tbl_employees.employee_id', '=', 'tbl_address.employee_id')
      ->where('tbl_employees.id', $id)
      ->select(
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
            'tbl_employees.buisness_id',
            'tbl_employees.team_id',
            'tbl_employees.skills_id',
            'tbl_address.address_id',
            'tbl_address.address',
            'tbl_address.city',
            'tbl_address.state',
            'tbl_address.pincode',
            'tbl_employees.status'
        )
        ->first();
      $data = array(
      "status" => 200,
      "response" =>$employeeData, 
      );
      return response()->json($data);
   }
   
    public function all_employee(Request $request)
   {
            $page = $request->get('page');
            if($page == 1){
              $offset = 0;
            } 
            else 
            {
              $offset = (($page-1) * 12);
            }
            
            $employeeData = Employee::join('tbl_address', 'tbl_employees.employee_id', '=', 'tbl_address.employee_id')
              ->join('tbl_role', 'tbl_role.roles_id', '=', 'tbl_employees.role_id')
              ->join('tbl_buisness', 'tbl_buisness.buisness_id', '=', 'tbl_employees.buisness_id')
             // ->join('tbl_users', 'tbl_users.id', '=', 'tbl_employees.team_id')
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
                    'tbl_employees.buisness_id',
                    'tbl_employees.team_id',
                    'tbl_employees.skills_id',
                    'tbl_address.country',
                    'tbl_address.address',
                    'tbl_address.city',
                    'tbl_address.state',
                    'tbl_address.pincode',
                    'tbl_role.roles_name',
                    'tbl_buisness.buisness_name',
                    //'tbl_users.name as teamname',
                    'tbl_employees.status'
                    
                    
                )
            ->where('tbl_employees.status', '1')
            ->skip($offset)
            ->take(12)
            ->get();
            
            
            $employeeDatacount = Employee::join('tbl_address', 'tbl_employees.employee_id', '=', 'tbl_address.employee_id')
              ->select(
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
                    'tbl_employees.buisness_id',
                    'tbl_employees.team_id',
                    'tbl_employees.skills_id',
                    'tbl_address.country',
                    'tbl_address.address',
                    'tbl_address.city',
                    'tbl_address.state',
                    'tbl_address.pincode',
                    'tbl_employees.status'
                    
                    
                )
            ->count();
              $data1 = array(
                  "status" => 200,
                  "data" =>$employeeData ,
                  "total" =>$employeeDatacount,
                  "per_page" => 12,
                  "page" => $page,
                  "offset" => $offset
                );
              
              return response()->json($data1);
    }
      
      
     public function update_employee(Request $request,$id)
   {
           if($request->file('image'))
            {
              $file= $request->file('image');
              $filename= date('YmdHi').$file->getClientOriginalName();
              $file->move(public_path('uploads'), $filename);
                  
              
              $update = Employee::where('id', $id)->update([
                    'employee_id' => $request->employee_id ,
                    'photo' => $filename ,
                    'first_name' => $request->first_name,
                    'last_name' =>$request->last_name,
                    'dateofbirth' =>$request->dateofbirth,
                    'gender' =>$request->gender,
                    'contactno' => $request->contactno,
                    'email' => $request->email,
                    'password' => md5($request->password),
                    'role_id' => $request->role_id,
                    'buisness_id' => $request->buisness_id,
                    'team_id' => $request->team_id,
                    'skills_id' => $request->skills_id,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
                
                $update = Address::where('employee_id', $request->employee_id)->update([
                    'address_id' => $request->address_id ,
                    'employee_id' => $request->employee_id ,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' =>$request->state,
                    'country' =>$request->country,
                    'pincode' =>$request->pincode,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
              $result = "Data updated Successfully!!!";
            }
            else
            {
                $update = Employee::where('id', $id)->update([
                    'employee_id' => $request->employee_id ,
                    'first_name' => $request->first_name,
                    'last_name' =>$request->last_name,
                    'dateofbirth' =>$request->dateofbirth,
                    'gender' =>$request->gender,
                    'contactno' => $request->contactno,
                    'email' => $request->email,
                    'password' => md5($request->password),
                    'role_id' => $request->role_id,
                    'buisness_id' => $request->buisness_id,
                    'team_id' => $request->team_id,
                    'skills_id' => $request->skills_id,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
                
                $update = Address::where('employee_id', $request->employee_id)->update([
                    'address_id' => $request->address_id ,
                    'employee_id' => $request->employee_id ,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' =>$request->state,
                    'country' =>$request->country,
                    'pincode' =>$request->pincode,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
                
                $result = "Data updated Successfully!!!";
            }
              
            $data = array(
              "status" => 200,
              "response" => $result, 
              );
  
        
      return response()->json($data);  
       
   }
   
   public function delete_employee($id)
    {
      $employeeId = Employee::where('id', $id)->value('employee_id');
      $update = Employee::where('employee_id',$employeeId)->update(['status'=>'0']);
      $update2 = Address::where('employee_id',$employeeId)->update(['status'=>'0']);
      $result = "Data Deleted Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
      
      return response()->json($data);
   }
    
  public function onlyTeamLead($tl_id)
  {
               $login = Employee::join('tbl_role', 'tbl_role.roles_id', '=', 'tbl_employees.role_id')
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
                                            'tbl_employees.team_id',
                                            'tbl_employees.skills_id',
                                            'tbl_role.roles_name',
                                            'tbl_employees.status'
                                        )
            ->WHERE('tbl_employees.buisness_id',$tl_id)
            ->WHERE('tbl_employees.status', '=', '1')
            ->get();
          
          return response([
                'status'=>'Success',
                'data'=>$login
              ]);
              
//   try{
          
//         $teamLeadData = Employee::with('roles')->whereHas('roles', function ($query) 
//         {
//             // Add your condition on roles here
//             $query->where('roles_name', 'department');
//         })->where('buisness_id',$tl_id)->get();
          
//       }catch(Exception $e)
//       {
//           return response([
//                 'status'=>'error',
//                 'Message'=>$e->getmessage()
//               ],400);
//       }
  }  
  public function onlyManager($bs_id){
      try{
          
          $teamLeadData = Employee::with('roles')->whereHas('roles', function ($query) {
            // Add your condition on roles here
            $query->where('roles_name', 'admin');
        })->where('buisness_id',$bs_id)->get();
        
          return response([
                'status'=>'Success',
                'data'=>$teamLeadData
              ]);
          
      }catch(Exception $e){
          return response([
                'status'=>'error',
                'Message'=>$e->getmessage()
              ],400);
      }
  }
  
  
}
