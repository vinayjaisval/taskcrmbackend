<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\CallerDesk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;
class AdminController extends Controller{
  public function index(){
   
  }

  // Department
  public function add_department(Request $request){

    $request->validate([
        'userid' => 'required',
        'email' => 'required',
        'contact_no' => 'required'
    ]);

    $userid_exists = Login::WHERE('userid', $request->userid)
            ->first();
    $phone_exists = Login::WHERE('phone', $request->contact_no)
            ->first();
    $email_exists = Login::WHERE('email', $request->email)
            ->first();
    if($userid_exists){
      $result = "<span class='text-danger'>User Id Already Exists. You can not save Department!!!</span>";
    } else if($phone_exists){
      $result = "<span class='text-danger'>Phone No Already Exists. You can not save Department!!!</span>";
    } else if($email_exists){
      $result = "<span class='text-danger'>Email Id Already Exists. You can not save Department!!!</span>";
    } else {
      $curtime = time();
      $api_key =  $curtime ."-" . rand("2222","9999");
      $api_key = md5($api_key);

      $department = new Login;
      $department->name = $request->name;
      $department->userid = $request->userid;
      $department->email = $request->email;
      $department->phone = $request->contact_no;
      $department->password = md5($request->user_pass);
      $department->api_key = $api_key;
      $department->user_img = '';
      $department->user_type = 'department';
      $department->status = '1';
      $department->is_deleted = '0';
      $department->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function department_list(Request $request){

    $page = $request->get('page');
    $keywords = $request->get('keywords');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $departments = Login::select('*')
        ->skip($offset)
        ->take(12)
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'department');
    if(!empty($keywords)){
      $departments = $departments->where(function ($query) use ($keywords) {
        $query->where('name', 'like', '%'.$keywords.'%')
              ->orWHERE('phone', 'like', '%'.$keywords.'%')
              ->orWHERE('userid', 'like', '%'.$keywords.'%')
              ->orWHERE('email', 'like', '%'.$keywords.'%');
        });
    }
    $departments =  $departments->orderby('id', 'DESC')->get();


    $departments_count = Login::select('id')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'department');
    if(!empty($keywords)){
      $departments_count = $departments_count->where(function ($query) use ($keywords) {
        $query->where('name', 'like', '%'.$keywords.'%')
              ->orWHERE('phone', 'like', '%'.$keywords.'%')
              ->orWHERE('userid', 'like', '%'.$keywords.'%')
              ->orWHERE('email', 'like', '%'.$keywords.'%');
        });
    }
    $departments_count =  $departments_count->orderby('id', 'DESC')->get();
    $departments_count = $departments_count->count();

    $data = array(
      "data" => $departments,
      "total" => $departments_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_department($id){
    $department = Login::where('id', $id)
             ->first();
    return response()->json($department); 
  }

  
  public function update_department($id, Request $request){
    $request->validate([
        'userid' => 'required',
    ]);

    $userid_exists = Login::WHERE('userid', $request->userid)
            ->WHERE('id', '!=', $id)
            ->first();
    $phone_exists = Login::WHERE('phone', $request->phone)
            ->WHERE('id', '!=', $id)
            ->first();
    $email_exists = Login::WHERE('email', $request->email)
            ->WHERE('id', '!=', $id)
            ->first();
    if($userid_exists){
      $data = "<span class='text-danger'>User Id Already Exists. You can not Update Department!!!</span>";
    } else if($phone_exists){
      $data = "<span class='text-danger'>Phone No Already Exists. You can not Update Department!!!</span>";
    } else if($email_exists){
      $data = "<span class='text-danger'>Email Id Already Exists. You can not Update Department!!!</span>";
    } else {

      $department = new Login;
      $department = Login::find($id);
      $department->name = $request->name;
      $department->phone = $request->phone;
      $department->email = $request->email;
      $department->userid = $request->userid;
      if(!empty($request->user_pass)){
        $department->password = md5($request->user_pass);
      }
      $department->save();
      $data = "Department Update Successfully!!!";
    }
    return response()->json($data); 
  }

  public function department_delete($id){

    $department = Login::find($id);
    $department->is_deleted = 1;
    $department->save();

    $data = "Department Deleted";
    return response()->json($data); 
  }


  public function all_department_list(Request $request){

    $departments = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'department')
        ->orderby('id', 'DESC')
        ->get();
    
    return response()->json($departments); 

  }

  public function all_department_list_dep($id, Request $request){

    $departments = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('id', $id)
        ->WHERE('user_type', 'department')
        ->orderby('id', 'DESC')
        ->get();
    
    return response()->json($departments); 

  }


  // Agent
  public function add_agent(Request $request){

    $request->validate([
        'userid' => 'required',
        'email' => 'required',
        'contact_no' => 'required'
    ]);

    $userid_exists = Login::WHERE('userid', $request->userid)
            ->first();
    $phone_exists = Login::WHERE('phone', $request->contact_no)
            ->first();
    $email_exists = Login::WHERE('email', $request->email)
            ->first();
    if($userid_exists){
      $result = "<span class='text-danger'>User Id Already Exists. You can not save Agent!!!</span>";
    } else if($phone_exists){
      $result = "<span class='text-danger'>Phone No Already Exists. You can not save Agent!!!</span>";
    } else if($email_exists){
      $result = "<span class='text-danger'>Email Id Already Exists. You can not save Agent!!!</span>";
    } else {
      $curtime = time();
      $api_key =  $curtime ."-" . rand("2222","9999");
      $api_key = md5($api_key);

      $agent = new Login;
      $agent->name = $request->name;
      $agent->userid = $request->userid;
      $agent->email = $request->email;
      $agent->phone = $request->contact_no;
      $agent->password = md5($request->user_pass);
      $agent->api_key = $api_key;
      $agent->pincode = $request->pincode;
      $agent->user_img = '';
      $agent->user_type = 'agent';
      $agent->status = '1';
      $agent->is_deleted = '0';
      $agent->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function agent_list(Request $request){

    $page = $request->get('page');
    $keywords = $request->get('keywords');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $agents = Login::select('*')
        ->skip($offset)
        ->take(12)
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent');
    if(!empty($keywords)){
      $agents = $agents->where(function ($query) use ($keywords) {
        $query->where('name', 'like', '%'.$keywords.'%')
              ->orWHERE('phone', 'like', '%'.$keywords.'%')
              ->orWHERE('userid', 'like', '%'.$keywords.'%')
              ->orWHERE('email', 'like', '%'.$keywords.'%');
        });
    }
    $agents =  $agents->orderby('id', 'DESC')->get();


    $agents_count = Login::select('id')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent');
    if(!empty($keywords)){
      $agents_count = $agents_count->where(function ($query) use ($keywords) {
        $query->where('name', 'like', '%'.$keywords.'%')
              ->orWHERE('phone', 'like', '%'.$keywords.'%')
              ->orWHERE('userid', 'like', '%'.$keywords.'%')
              ->orWHERE('email', 'like', '%'.$keywords.'%');
        });
    }
    $agents_count =  $agents_count->orderby('id', 'DESC')->get();
    $agents_count = $agents_count->count();

    $data = array(
      "data" => $agents,
      "total" => $agents_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_agent($id){
    $agent = Login::where('id', $id)
             ->first();
    return response()->json($agent); 
  }

  
  public function update_agent($id, Request $request){
    $request->validate([
        'userid' => 'required',
    ]);

    $userid_exists = Login::WHERE('userid', $request->userid)
            ->WHERE('id', '!=', $id)
            ->first();
    $phone_exists = Login::WHERE('phone', $request->phone)
            ->WHERE('id', '!=', $id)
            ->first();
    $email_exists = Login::WHERE('email', $request->email)
            ->WHERE('id', '!=', $id)
            ->first();
    if($userid_exists){
      $data = "<span class='text-danger'>User Id Already Exists. You can not Update Agent!!!</span>";
    } else if($phone_exists){
      $data = "<span class='text-danger'>Phone No Already Exists. You can not Update Agent!!!</span>";
    } else if($email_exists){
      $data = "<span class='text-danger'>Email Id Already Exists. You can not Update Agent!!!</span>";
    } else {

      $agent = new Login;
      $agent = Login::find($id);
      $agent->name = $request->name;
      $agent->phone = $request->phone;
      $agent->email = $request->email;
      $agent->userid = $request->userid;
      if(!empty($request->user_pass)){
        $agent->password = md5($request->user_pass);
      }
      $agent->pincode = $request->pincode;
      $agent->save();
      $data = "Agent Update Successfully!!!";
    }
    return response()->json($data); 
  }

  public function agent_delete($id){

    $agent = Login::find($id);
    $agent->is_deleted = 1;
    $agent->save();

    $data = "Agent Deleted";
    return response()->json($data); 
  }

  public function all_agent_list(Request $request){

    $agents = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent')
        ->orderby('id', 'DESC')
        ->get();
    return response()->json($agents); 

  }

  public function all_agent_list_agent($id, Request $request){

    $agents = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('id', $id)
        ->WHERE('user_type', 'agent')
        ->orderby('id', 'DESC')
        ->get();
    return response()->json($agents); 

  }


  



  

  // Profile Update
  public function end_edit_user($id){
    $agent = Login::where('id', $id)
             ->first();
    return response()->json($agent); 
  }

  public function update_profile($id, Request $request){
    $request->validate([
        'userid' => 'required',
    ]);

    $userid_exists = Login::WHERE('userid', $request->userid)
            ->WHERE('id', '!=', $id)
            ->first();
    $phone_exists = Login::WHERE('phone', $request->phone)
            ->WHERE('id', '!=', $id)
            ->first();
    $email_exists = Login::WHERE('email', $request->email)
            ->WHERE('id', '!=', $id)
            ->first();
    if($userid_exists){
      $data = "<span class='text-danger'>User Id Already Exists. You can not Update End User!!!</span>";
    } else if($phone_exists){
      $data = "<span class='text-danger'>Phone No Already Exists. You can not Update End User!!!</span>";
    } else if($email_exists){
      $data = "<span class='text-danger'>Email Id Already Exists. You can not Update End User!!!</span>";
    } else {

      $user = new Login;
      $user = Login::find($id);
      $user->name = $request->name;
      $user->phone = $request->phone;
      $user->email = $request->email;
      $user->userid = $request->userid;
      if(!empty($request->password)){
        $user->password = md5($request->password);
      }
      $user->save();
      $data = "Profile Update Successfully!!!";
    }
    return response()->json($data); 
  }

  

 

  // Add Astro to Caller Desk
  public function add_ast_caller(Request $request){

    $data = [
      'member_name' => $request->name,
      'member_num' => $request->phone,
      'member_email' => $request->email,
      'access' => '2',
      'status' => '1',
      'authcode' => '2c463718d70809dad964904823ae0f29'
    ];

    $response = Http::post('https://app.callerdesk.io/api/addmember_V2', $data);

    return response($response);

  }

  // List Astro to Caller Desk
  public function getcallerdeskmemberlist(Request $request){

    $client = new Client(['base_uri' => 'https://app.callerdesk.io/']);
    $response = $client->request('POST', '/api/getmemberlist_V2', ['form_params' => [
      'authcode' => '2c463718d70809dad964904823ae0f29'
    ]]);

    echo $response->getBody();

  }



  public function all_memebr_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    
    $members = Login::latest()
            ->skip($offset)
            ->take(12)
            ->WHERE('is_deleted', '0')
            ->WHERE('user_type', '!=', 'superadmin')
            ->get();

    $members_count = Login::All()
            ->WHERE('is_deleted', '0')
            ->WHERE('user_type', '!=', 'superadmin');

    $members_count = $members_count->count();

    $data = array(
      "data" => $members,
      "total" => $members_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function memebr_block($id){

    $user = Login::find($id);
    $user->status = 2;
    $user->save();

    $data = "Member Blocked Successfully !!";
    return response()->json($data); 
  }

  public function memebr_active($id){

    $user = Login::find($id);
    $user->status = 1;
    $user->save();

    $data = "Member Activated Successfully !!!";
    return response()->json($data); 
  }

  public function all_department_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.userid FROM `tbl_lead` a, tbl_users b WHERE 1 AND a.department = b.id GROUP BY a.department, b.userid;";
    $department=DB::select($sql);
    return response()->json($department);

  }
//   public function addModule(request $request){
//       return response([
//                         'status' => "success",
//                         ], 200);
//       try
//       {
//               $validate = $request->validate([
//                      'project_id'=>'required',
//                      'module_name'=>'required',
//                      'start_date'=>'required',
//                      'end_date'=>'required'
//                   ]);
//               if($validate){
//                   $data = [
//                         'project_id'=>$request->project_id,
//                         'module_name'=>$request->module_name,
//                         'start_date'=>$request->start_date,
//                         'end_date'=>$request->end_date
//                       ];
//                   $insertData = DB::table('module')->insert($data); 
//                   return response([
//                         'status' => "success",
//                         'statuscode' => 200,
//                         'message' => __('Your article has been succussfully added, After approval by admin then will show in article list.'),
//                         'data' =>$data
//                     ], 200);
//               }
//       }catch(Exception $e){
//           return response(['status' => "error", 'statuscode' => 500, 'message' => $ex->get/Message().' '.$ex->getmessage()], 500);
//       }
//   }
  
  

  
}
