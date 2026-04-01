<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\CallerDesk;
use App\Models\Project;
use App\Models\TaskModules;
use App\Models\Employee;
use App\Models\Roles;
use App\Models\Module;
use App\Models\Tasktodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;

class ProjectController extends Controller
{
    
    public function start_project(Request $request)
   {
     // Update the project status and other fields
    $update = Project::where('project_id', $request->project_id)->update([
        'project_status' => $request->project_status,
        'updated_by' => $request->updated_by, // or $request->user()->id if you want to store user id
        'updated_at' => now(),
    ]);

    // Prepare the response
    $result = "Project started successfully!!!";
    $data = [
        "status" => 200,
        "response" => $result,
    ];

    // Return JSON response
    return response()->json($data);
   }
    
     public function add_projectav(Request $request)
   {
     
           $request->validate
           ([
                'project_name' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'buisness_id' => 'required',
                'details' => 'required',
                'client_name' => 'required',
            ]);
            
        if($request->file('document'))
        {
          $file= $request->file('document');
         // $filename= date('YmdHi').$file->getClientOriginalName();
          $filename = date('YmdHi') . str_replace(' ', '_', $file->getClientOriginalName());
          $file->move(public_path('uploads'), $filename);
          
          define('Bs', 'project');
          $project = new Project;
          $project->project_id = Bs.substr(uniqid(), -3);
          $project->project_name = $request->project_name;
          $project->start_date = $request->start_date;
          $project->end_date = $request->end_date;
          $project->total_time = $request->total_time;
          $project->buisness_id = $request->buisness_id;
          $project->manager_id = $request->manager_id;
          $project->teamleader_id = $request->leader_id;
          $project->client_name = $request->client_name;
          $project->client_phone = $request->client_phone;
          $project->client_email = $request->client_email;
          $project->document  = $filename;
          $project->url= $request->url;
          $project->uat_url= $request->uat_url;
          $project->testing_url= $request->testing_url;
           if(is_array($request->skills))
              {
                  $skills = implode(", ", $request->skills);
              }else{
                  $skills = $request->skills;
              }
          $project->skills=$skills ; 
          $project->Details= $request->details;
          $project->task_status= $request->status;
          $project->created_at=date('m/d/Y h:i:s a', time());
          $project->status ='1';
          $project->save();
          $result = "Data Store Successfully!!!";
        }
        
        else{
            
          define('Bs', 'project');
          $project = new Project;
          $project->project_id = Bs.substr(uniqid(), -3);
          $project->project_name = $request->project_name;
          $project->start_date = $request->start_date;
          $project->end_date = $request->end_date;
          $project->total_time = $request->total_time;
          $project->buisness_id = $request->buisness_id;
          $project->manager_id = $request->manager_id;
          $project->teamleader_id = $request->leader_id;
          $project->client_name = $request->client_name;
          $project->client_phone = $request->client_phone;
          $project->client_email = $request->client_email;
          $project->url= $request->url;
          $project->uat_url= $request->uat_url;
          $project->testing_url= $request->testing_url;
          if(is_array($request->skills))
              {
                  $skills = implode(", ", $request->skills);
              }else{
                  $skills = $request->skills;
              }
          $project->skills=$skills ; 
          $project->Details= $request->details;
          $project->task_status= $request->status;
          $project->created_at=date('m/d/Y h:i:s a', time());
          $project->status ='1';
          $project->save();
          $result = "Data Store Successfully!!!";
        }
            
          
          
          $data = array(
          "status" => 200,
          "response" =>$result, 
          );
            
          return response()->json($data);
        
   }
   
   public function edit_projectav($id)
   {
      
      $project = Project::where('id', $id)->first();
      $data = array(
      "status" => 200,
      "response" =>$project, 
      );
      return response()->json($data);
   }
   
    public function all_projectav(Request $request, $id='null')
   {
            $page = $request->get('page');
            if($page == 1){
              $offset = 0;
            } 
            else 
            {
              $offset = (($page-1) * 12);
            }
            if($id == 'null')
            {
              $data = Project::with('bussiness','managree','teamlead')->WHERE('status', '1')
              ->skip($offset)
              ->take(12)
              ->get();
              $buisness_count = Project::select('buisness_id')
                ->where('status', '1')
                ->count();
            }
            else
            {
                $role = Employee::where('employee_id',$id)->first();
                $rolename = Roles::where('roles_id',$role['role_id'])->first();
                if($rolename['roles_name']=='teamleader')
                {
                      $data = Project::with('bussiness')->WHERE('status', '1')
                      ->WHERE('teamleader_id', $id)
                      ->skip($offset)
                      ->take(12)
                      ->get();
                      $buisness_count = Project::select('buisness_id')
                        ->WHERE('teamleader_id', $id)
                        ->where('status', '1')
                        ->count();
                      
                    
                }
                else
                {
                      $data = Project::with('bussiness')->WHERE('status', '1')
                      ->WHERE('manager_id', $id)
                      ->skip($offset)
                      ->take(12)
                      ->get();
                      $buisness_count = Project::select('buisness_id')
                        ->WHERE('manager_id', $id)
                        ->where('status', '1')
                        ->count();
                     
                       
                }
                
                
            }
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
               if(is_array($request->skills))
              {
                  $skills = implode(", ", $request->skills);
              }else{
                  $skills = $request->skills;
              }
              $update = Project::where('id', $id)->update([
                    'project_id' => $request->project_id,
                    'project_name' => $request->project_name,
                    'start_date' => $request->start_date,
                    'end_date' =>$request->end_date,
                    'total_time' => $request->total_time,
                    'buisness_id' =>$request->buisness_id,
                    'manager_id' =>$request->manager_id,
                    'client_name' => $request->client_name,
                    'client_phone' => $request->client_phone,
                    'client_email' => $request->manager_id,
                    'document' =>$filename,
                    'url' =>$request->url,
                    'uat_url'=> $request->uat_url,
                    'testing_url'=> $request->testing_url,
                    'skills' =>$skills,
                    'details' =>$request->details,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
              $result = "Data updated Successfully!!!";
            }
            else
            {
                 if(is_array($request->skills))
              {
                  $skills = implode(", ", $request->skills);
              }else{
                  $skills = $request->skills;
              }
                $update = Project::where('id', $id)->update([
                    'project_id' => $request->project_id,
                    'project_name' => $request->project_name,
                    'start_date' => $request->start_date,
                    'end_date' =>$request->end_date,
                    'total_time' => $request->total_time,
                    'buisness_id' =>$request->buisness_id,
                    'manager_id' =>$request->manager_id,
                    'client_name' => $request->client_name,
                    'client_phone' => $request->client_phone,
                    'client_email' => $request->manager_id,
                    'url' =>$request->url,
                    'uat_url'=> $request->uat_url,
                    'testing_url'=> $request->testing_url,
                    'skills' =>$skills,
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
   
   public function get_projectbuis($id, Request $request)
   {
      $project = Project::where('buisness_id', $id)->get();
      $data = array(
      "status" => 200,
      "response" =>$project, 
      );
      return response()->json($data);
   }
    
//   mdules function
   public function GetModule(){
       
       try{
           
           $moduleData = Module::with('bussiness','projectt')->paginate(10);
           return response([
                 'status' => 'success',
                 'data' => $moduleData
               ],200);
           
       }catch(Exception $e){
           return response([
                 'status'=>'error',
                 'error' => $e->getmessage(),
               ],400);
       }
   } 
   public function GetTasks(){
       
       try{
           
           $TaskData = Tasktodo::with('module','projectt')->paginate(10);
           return response([
                 'status' => 'success',
                 'data' => $TaskData
               ],200);
           
       }catch(Exception $e){
           return response([
                 'status'=>'error',
                 'error' => $e->getmessage(),
               ],400);
       }
   } 
   
  
  
}
