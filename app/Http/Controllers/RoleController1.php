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
      $roles = Roles::where('id',$id)->first();
      $data = array(
      "status" => 200,
      "response" =>$roles, 
      );
      return response()->json($data);
   }
   
    public function all_projectav(Request $request)
   {
            $page = $request->get('page');
            if($page == 1){
              $offset = 0;
            } 
            else 
            {
              $offset = (($page-1) * 12);
            }
              $data = Project::WHERE('status', '1')
              ->skip($offset)
              ->take(12)
              ->get();
              $buisness_count = Project::select('buisness_id')
                ->where('status', '1')
                ->count();
              $data1 = array(
                  "status" => 200,
                  "data" => $data,
                  "total" =>$buisness_count,
                  "per_page" => 12,
                  "page" => $page,
                  "offset" => $offset
                );
              
              return response()->json($data1);
    }
    
    public function update_projectav(Request $request,$id)
    {
       
             if($request->file('document'))
            {
              $file= $request->file('document');
              $filename= date('YmdHi').$file->getClientOriginalName();
              $file->move(public_path('uploads'), $filename);
              $update = Project::where('id', $id)->update([
                    'project_id' => $request->project_id,
                    'project_name' => $request->project_name,
                    'start_date' => $request->start_date,
                    'end_date' =>$request->end_date,
                    'total_time' => $request->total_time,
                    'buisness_id' =>$request->buisness_id,
                    'manager_id' =>$request->manager_id,
                    'document' =>$filename,
                    'url' =>$request->url,
                    'skills' =>implode(", ", $request->skills),
                    'details' =>$request->details,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
              $result = "Data updated Successfully!!!";
            }
            else
            {
                $update = Project::where('id', $id)->update([
                    'project_id' => $request->project_id,
                    'project_name' => $request->project_name,
                    'start_date' => $request->start_date,
                    'end_date' =>$request->end_date,
                    'total_time' => $request->total_time,
                    'buisness_id' =>$request->buisness_id,
                    'manager_id' =>$request->manager_id,
                    'url' =>$request->url,
                    'skills' =>implode(", ", $request->skills),
                    'details' =>$request->details,
                    'status'=>'1',
                    'updated_at' => now(),
                ]);
                
                $result = "Data updated Successfully!!!";
            }
              
            $data = array(
              "status" => 200,
              "response" => $result, 
              );
            return response()->json($data);

   }
   public function delete_projectav($id)
    {
      $update = Project::where('id',$id)->update(['status'=>'0']);
      $result = "Data Deleted Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
      
      return response()->json($data);
   }
 
  
}
