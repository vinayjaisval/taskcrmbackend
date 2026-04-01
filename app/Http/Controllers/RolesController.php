<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\Roles;
use App\Models\CallerDesk;
use App\Models\TaskModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;
class RolesController extends Controller{
  
    public function add_roles(Request $request)
   {
           $request->validate
           ([
                'roles_name' => 'required',
            ]);

          define('Bs', 'role');
          $roles = new Roles;
          $roles->roles_id = Bs.substr(uniqid(), -3);
          $roles->roles_name = $request->roles_name;
          $roles->created_at=now();
          $roles->status ='1';
          $roles->save();
          $result = "Data Store Successfully!!!";
          
          $data = array(
          "status" => 200,
          "response" =>$result, 
          );
            
          return response()->json($data);
        
   }
   
   public function edit_roles($id)
   {
      
      $project = Roles::where('id', $id)->first();
      $data = array(
      "status" => 200,
      "response" =>$project, 
      );
      return response()->json($data);
   }
   
    public function all_roles(Request $request)
  {
            $page = $request->get('page');
            if($page == 1){
              $offset = 0;
            } 
            else 
            {
              $offset = (($page-1) * 12);
            }
              $data = Roles::WHERE('status', '1')
              ->skip($offset)
              ->take(12)
              ->get();
              $roles_count = Roles::select('buisness_id')
                ->where('status', '1')
                ->count();
              $data1 = array(
                  "status" => 200,
                  "data" => $data,
                  "total" =>$roles_count,
                  "per_page" => 12,
                  "page" => $page,
                  "offset" => $offset
                );
              
              return response()->json($data1);
    }
    
    public function update_roles(Request $request,$id)
    {
       
                $update = Roles::where('id', $id)->update([
                    'Roles_id' => $request->roles_id,
                    'Roles_name' => $request->roles_name,
                    'status'=>'1',
                    'updated_at' => now(),
                ]);
                
                $result = "Data updated Successfully!!!";

              
            $data = array(
              "status" => 200,
              "response" => $result, 
              );
            return response()->json($data);

  }
  public function delete_roles($id)
    {
      $update = Roles::where('id',$id)->update(['status'=>'0']);
      $result = "Data Deleted Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
      
      return response()->json($data);
  }
 
  
}
