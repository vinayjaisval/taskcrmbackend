<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\CallerDesk;
use App\Models\Module;
use App\Models\Project;
use App\Models\TaskModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;

class ModuleController extends Controller
{
    
     public function add_module1(Request $request)
   {
          $request->validate
          ([
                
                'project_id' => 'required',
                'module_name' => 'required',
                //'start_date' => 'required',
                //'end_date' => 'required',
                'description' => 'required',
            ]);
          $maxtime = project::select('total_time')->where('project_id',$request->project_id)->where('status', '1')->first();
          
          $totalTime = Module::where('project_id', $request->project_id)->where('status', '1')->sum('working_hrs');
          
          $actualtt = $totalTime + $request->working_hrs;
    
          
          if(($request->working_hrs > $maxtime['total_time']) || ($actualtt > $maxtime['total_time']))
          {
               $result = "Module Time Exceding The Limits Of Project!!! ";
               $data = array
               (
                  "status" => 400,
                  "response" =>$result, 
               );
          }
          else
          {
              define('Md', 'Module');
              $Module = new Module;
              $Module->Module_id = Md.substr(uniqid(), -3);
              $Module->project_id = $request->project_id;
              $Module->buisness_id = $request->buisness_id;
              $Module->module_name = $request->module_name;
              $Module->start_date = $request->start_date;
              $Module->end_date = $request->end_date;
              $Module->working_hrs = $request->working_hrs;
              $Module->module_weight = $request->module_weight;
              $Module->description  = $request->description;
              $Module->status ='1';
              $Module->created_at = now();
              $Module->save();
              $result = "Data Store Successfully!!!";
              $data = array
               (
                  "status" => 200,
                  "response" =>$result, 
                );
              
           }
           return response()->json($data);
          
        
   }
   
   public function edit_Module1($id)
   {
      
      $Module = Module::with('bussiness','projectt')->where('id', $id)->first();
      $data = array(
      "status" => 200,
      "response" =>$Module, 
      );
      return response()->json($data);
   }
   
    public function all_Module1(Request $request,$id="null")
   {
            $page = $request->get('page');
            if($page == 1){
              $offset = 0;
            } 
            else 
            {
              $offset = (($page-1) * 12);
            }
            
            if($id=='null')
            {
                   $data = Module::with('bussiness','projectt')->WHERE('status', '1')
                  ->skip($offset)
                  ->take(12)
                  ->get();
                  $buisness_count = Module::select('buisness_id')
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
                
            }
            else{
                
                  $data = Module::with('bussiness','projectt')->WHERE('status', '1')
                  ->where('project_id', $id)
                  ->skip($offset)
                  ->take(12)
                  ->get();
                  $buisness_count = Module::select('buisness_id')
                    ->where('status', '1')
                    ->where('project_id', $id)
                    ->count();
                  $data1 = array(
                      "status" => 200,
                      "data" => $data,
                      "total" =>$buisness_count,
                      "per_page" => 12,
                      "page" => $page,
                      "offset" => $offset
                    );
            }
              
              
              return response()->json($data1);
    }
    
    public function update_Module1(Request $request,$id)
    {
                $update = Module::where('id', $id)->update([
                    'module_id' => $request->module_id,
                    'project_id' => $request->project_id,
                    'buisness_id' => $request->buisness_id,
                    'module_name' => $request->module_name,
                    'start_date' =>$request->start_date,
                    'end_date' => $request->end_date,
                    'working_hrs' =>$request->working_hrs,
                    'module_weight' =>$request->module_weight,
                    'description' => $request->description,
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
   public function delete_Module1($id)
    {
      $update = Module::where('id',$id)->update(['status'=>'0']);
      $result = "Data Deleted Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
      
      return response()->json($data);
   }
   
  
  
}
