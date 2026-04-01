<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Skills;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller{
 
     public function add_statusnew(Request $request)
  {
   
    $request->validate([
        'role_id' => 'required',
        'status_name' => 'required'
    ]);

      define('St', 'Status');
      $Status = new Status;
      $status_id = St.substr(uniqid(), -3);
      $Status->status_id = $status_id;
      $Status->role_id = $request->role_id;
      $Status->status_name = $request->status_name;
      $Status->status ='1';
      $Status->created_at = date('m/d/Y h:i:s a', time());
      $Status->save();
      $result = "Data Store Successfully!!!";
     
     $data = array(
      "status" => 200,
      "response" =>$result, 
      );
    return response()->json($data);

  }
  
  public function edit_statusnew($id)
   {
      
      $status = Status::where('id', $id)->first();
      $data = array(
      "status" => 200,
      "response" =>$status, 
      );
      return response()->json($status);
   }
   
   public function all_statusnew(Request $request,$id='null')
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
                  $data = Status::with('roless')->WHERE('status', '1')
                  ->skip($offset)
                  ->take(12)
                  ->get();
                  $status_count = Status::select('status_id')
                    ->where('status', '1')
                    ->count();
                  $data1 = array(
                      "status" => 200,
                      "data" => $data,
                      "total" =>$status_count,
                      "per_page" => 12,
                      "page" => $page,
                      "offset" => $offset
                    );
            }
            else{
                 $data = Status::with('roless')->WHERE('role_id', $id)->WHERE('status','1')
                  ->skip($offset)
                  ->take(12)
                  ->get();
                  $status_count = Status::select('status_id')->WHERE('role_id',$id)
                    ->where('status', '1')
                    ->count();
                  $data1 = array(
                      "status" => 200,
                      "data" => $data,
                      "total" =>$status_count,
                      "per_page" => 12,
                      "page" => $page,
                      "offset" => $offset
                    );
                
            }
              
              return response()->json($data1);
    }
    
    
     public function update_statusnew(Request $request,$id)
    {
            
                $update = Status::where('id', $id)->update([
                    'status_id' => $request->status_id,
                    'role_id' => $request->role_id,
                    'status_name' =>$request->status_name,
                    'status'=>'1',
                    'updated_at' =>now(),
                    // add more fields as needed
                ]);
                $result = "Data updated Successfully!!!";
                $data = array(
                  "status" => 200,
                  "response" => $result, 
                  );
  
                return response()->json($data);

   }
   
   public function delete_statusnew($id)
    {
      $update = Status::where('id',$id)->update(['status'=>'0']);
      $result = "Data Deleted Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
      
      return response()->json($data);
   }
  
  
  




  
}
