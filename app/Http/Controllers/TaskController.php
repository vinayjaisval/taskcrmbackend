<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\CallerDesk;
use App\Models\Tasktodo;
//use App\Models\TaskModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;

class TaskController extends Controller
{
    
     public function add_task1(Request $request)
   {
    
    
          $request->validate
          ([
                //'module_id' => 'required',
                'task_name' => 'required',
                'description' => 'required'
           ]);
           
    
          define('Ts', 'Task');
          $tasktodo = new Tasktodo;
          $tasktodo->task_id= Ts.substr(uniqid(), -3);
          $tasktodo->module_id= $request->module_id;
          $tasktodo->project_id = $request->project_id;
          $tasktodo->task_name = $request->task_name;
          $tasktodo->description = $request->description;
          $tasktodo->status ='1';
          $tasktodo->created_at= now();
          $tasktodo->save();
          $result = "Task Store Successfully!!!";
        
          $data = array(
          "status" => 200,
          "response" =>$result, 
          );
            
          return response()->json($data);
        
   }
   
   public function edit_task1($id)
   {
  
      
      $tasktodo = Tasktodo::where('id', $id)->first();
      $data = array(
      "status" => 200,
      "response" =>$tasktodo, 
      );
      return response()->json($data);
   }
   
    public function all_task1(Request $request,$id='null')
   {
            $page = $request->get('page');
            if($page == 1){
              $offset = 0;
            } 
            else 
            {
              $offset = (($page-1) * 12);
            }
             if($id='null')
            {
               $data = Tasktodo::with('module','projectt')->WHERE('status', '1')->with('module')
              ->skip($offset)
              ->take(12)
              ->get();
              $buisness_count = Tasktodo::select('buisness_id')
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
            else
            {
                 $data = Tasktodo::with('module','projectt')->WHERE('status', '1')->WHERE('module_id', $id)->with('module')
              ->skip($offset)
              ->take(12)
              ->get();
              $buisness_count = Tasktodo::select('buisness_id')
                 ->WHERE('module_id', $id)
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
              
              return response()->json($data1);
    }
    
    public function update_task1(Request $request,$id)
    {
       
              $update = Tasktodo::where('id', $id)->update([
                    'task_id' => $request->task_id,
                    'module_id' => $request->module_id,
                    'project_id' => $request->project_id,
                    'task_name' =>$request->task_name,
                    'description' =>$request->description,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
                
                $result = "Task updated Successfully!!!";
                $data = array(
                  "status" => 200,
                  "response" => $result, 
                  );
                return response()->json($data);

   }
   
   public function delete_task1($id)
   {
      $update = Tasktodo::where('id',$id)->update(['status'=>'0']);
      $result = "Data Deleted Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
      
      return response()->json($data);
   }
   
  
  
}
