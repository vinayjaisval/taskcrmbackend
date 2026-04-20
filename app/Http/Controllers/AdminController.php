<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Login;
use App\Models\CallerDesk;
use App\Models\TaskModules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;
class AdminController extends Controller{
  public function index(){
   
  }

  // Project
  public function add_project(Request $request){

    $request->validate([
        'name' => 'required'
    ]);
    
    // Assigneess --
    $ass_type = gettype($request->assignee);
    if($ass_type == "string"){
        $assignees = $request->assignee;
        $ass_arr = explode (",", $assignees); 
        $countAssignee = count($ass_arr);

    } else {
        if(isset($request->assignee)){
            $assignees = implode(",",$request->assignee);
            $countAssignee = count($request->assignee);
        } else {
            $assignees = "";
            $countAssignee = 0;
        }
    }
    
    
    // Skillss --
    $ass_type = gettype($request->skills);
    if($ass_type == "string"){
        $skillss = $request->skills;
        $skills_arr = explode (",", $skillss); 
        $countSkill = count($skills_arr);

    } else {
        if(isset($request->skills)){
            $skillss = implode(",",$request->skills);
            $countSkill = count($request->skills);
        } else {
            $skillss = "";
            $countSkill = 0;
        }
    }

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
      $department->phone = $request->phone;
      $department->password = md5($request->password);
      $department->total_task = $request->total_task;
      $department->lead_by = $request->lead_by;
      $department->start_date = $request->start_date;
      $department->end_date = $request->end_date;
      
      $department->skills = $skillss;
      $department->assignee = $assignees;
      $department->remarks = $request->remarks;
      $department->status = $request->status;
      
      $department->api_key = $api_key;
      $department->user_img = '';
      $department->user_type = 'project';
      $department->is_deleted = '0';
      $department->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function project_list(Request $request){

  

    $page = $request->get('page');
    $keywords = $request->get('keywords');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

   $departments = Login::from('tbl_users as l1')
    ->select('l1.*', 'l2.name as lead_by')
    ->leftJoin('tbl_users as l2', 'l1.lead_by', '=', 'l2.id')
    ->where('l1.is_deleted', '0')
    ->where('l1.user_type', 'project')
    ->skip($offset)
    ->take(12);

if (!empty($keywords)) {
    $departments = $departments->where(function ($query) use ($keywords) {
        $query->where('l1.name', 'like', '%' . $keywords . '%')
              ->orWhere('l1.phone', 'like', '%' . $keywords . '%')
              ->orWhere('l1.userid', 'like', '%' . $keywords . '%')
              ->orWhere('l1.email', 'like', '%' . $keywords . '%');
    });
}

$departments = $departments->orderBy('l1.id', 'DESC')->get();
    


    $departments_count = Login::select('id')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'project');
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

  public function edit_project($id){
    $department = Login::where('id', $id)
             ->first();
    return response()->json($department); 
  }

  
  public function update_project($id, Request $request){
    $request->validate([
        'userid' => 'required',
    ]);
    
    
    // Assigneess --
    $ass_type = gettype($request->assignee);
    if($ass_type == "string"){
        $assignees = $request->assignee;
        $ass_arr = explode (",", $assignees); 
        $countAssignee = count($ass_arr);

    } else {
        if(isset($request->assignee)){
            $assignees = implode(",",$request->assignee);
            $countAssignee = count($request->assignee);
        } else {
            $assignees = "";
            $countAssignee = 0;
        }
    }
    
    
    // Skillss --
    $ass_type = gettype($request->skills);
    if($ass_type == "string"){
        $skillss = $request->skills;
        $skills_arr = explode (",", $skillss); 
        $countSkill = count($skills_arr);

    } else {
        if(isset($request->skills)){
            $skillss = implode(",",$request->skills);
            $countSkill = count($request->skills);
        } else {
            $skillss = "";
            $countSkill = 0;
        }
    }

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
      if(!empty($request->password)){
        $department->password = md5($request->password);
      }
      $department->total_task = $request->total_task;
      $department->lead_by = $request->lead_by;
      $department->start_date = $request->start_date;
      $department->end_date = $request->end_date;
      
      $department->skills = $skillss;
      $department->assignee = $assignees;
      $department->remarks = $request->remarks;
      $department->status = $request->status;
      
      $department->save();
      $data = "Project Update Successfully!!!";
    }
    return response()->json($data); 
  }

  public function project_delete($id){

    $department = Login::find($id);
    $department->is_deleted = 1;
    $department->save();

    $data = "Project Deleted";
    return response()->json($data); 
  }


  public function all_project_list(Request $request){

    $departments = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'project')
        ->orderby('name', 'ASC');

    if(isset($request->projectid)){
        $departments = $departments->WHERE('id', $request->projectid);
    }
    if(isset($request->lead_by)){
        $departments = $departments->WHERE('lead_by', $request->lead_by);
    }
    $departments = $departments->get();
    
    return response()->json($departments); 

  }

  public function all_project_list_dep($id, Request $request){

    $departments = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('id', $id)
        ->WHERE('user_type', 'project')
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
        
        
        // Skills --
        $ass_type = gettype($request->skills);
        if($ass_type == "string"){
            $skillss = $request->skills;
            $ass_arr = explode (",", $skillss); 
            $countSkills = count($ass_arr);
    
        } else {
            if(isset($request->skills)){
                $skillss = implode(",",$request->skills);
                $countSkills = count($request->skills);
            } else {
                $skillss = "";
                $countSkills = 0;
            }
        }
        
        
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
      $agent->skills = $skillss;
      $agent->lead_by =$request->lead_by;
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
        
        
        // Skills --
        $ass_type = gettype($request->skills);
        if($ass_type == "string"){
            $skillss = $request->skills;
            $ass_arr = explode (",", $skillss); 
            $countSkills = count($ass_arr);
    
        } else {
            if(isset($request->skills)){
                $skillss = implode(",",$request->skills);
                $countSkills = count($request->skills);
            } else {
                $skillss = "";
                $countSkills = 0;
            }
        }

      $agent = new Login;
      $agent = Login::find($id);
      $agent->name = $request->name;
      $agent->phone = $request->phone;
      $agent->email = $request->email;
      $agent->userid = $request->userid;
      $agent->skills = $skillss;
      if(!empty($request->user_pass)){
        $agent->password = md5($request->user_pass);
      }
      $agent->pincode = $request->pincode;
      $agent->lead_by =$request->lead_by;
      $agent->save();
      $data = "Agent Update Successfully!!!";
    }
    return response()->json($data); 
  }

  public function agent_delete($id){
      
      
    $system_ip = $this->system_ip();

    $agent = Login::find($id);
    $agent->is_deleted = 1;
    $agent->system_ip = $system_ip;
    $agent->save();

    $data = "Agent Deleted";
    return response()->json($data); 
  }
  
  public  function system_ip(){
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
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

  // Admin Agent =====


  public function add_agent_admin($id, Request $request){

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
      $agent->lead_by = $id;
      $agent->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function agent_list_admin($id, Request $request){

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
        ->WHERE('user_type', 'agent')
        ->WHERE('lead_by', $id);
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
        ->WHERE('user_type', 'agent')
        ->WHERE('lead_by', $id);
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

  public function all_agent_list_admin($id, Request $request){

    $agents = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent')
        ->orderby('id', 'DESC')
        ->WHERE('lead_by', $id)
        ->get();
    return response()->json($agents); 

  }


  // Team Lead
  public function add_teamlead(Request $request){

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
      $agent->user_type = 'admin';
      $agent->status = '1';
      $agent->is_deleted = '0';
      $agent->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function teamlead_list(Request $request){

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
        ->WHERE('user_type', 'admin');
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
        ->WHERE('user_type', 'admin');
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

  public function edit_teamlead($id){
    $agent = Login::where('id', $id)
             ->first();
    return response()->json($agent); 
  }

  
  public function update_teamlead($id, Request $request){
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

  public function teamlead_delete($id){

    $agent = Login::find($id);
    $agent->is_deleted = 1;
    $agent->save();

    $data = "Agent Deleted";
    return response()->json($data); 
  }

  public function all_teamlead_list(Request $request){

    $agents = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'admin')
        ->orderby('id', 'DESC')
        ->get();
    return response()->json($agents); 

  }

  

  public function all_teamlead_list_agent($id, Request $request){

    $agents = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('id', $id)
        ->WHERE('user_type', 'admin')
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
    $keywords = $request->get('keywords');
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
    if(!empty($keywords))
    {
      $members =  Login::where('name', 'like', '%'.$keywords.'%')
              ->orWHERE('phone', 'like', '%'.$keywords.'%')
              ->orWHERE('userid', 'like', '%'.$keywords.'%')
              ->orWHERE('email', 'like', '%'.$keywords.'%')->take(12)->WHERE('is_deleted', '0')->WHERE('user_type','!=','superadmin')->get();
    }
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

  public function all_department_count_old(Request $request){

    $sql = "SELECT count(a.id) as countID, b.userid, b.total_task, a.project 
    FROM `tbl_lead` a, tbl_users b 
    WHERE 1 AND a.status = '3' AND a.project = b.id 
    GROUP BY a.project, b.userid, b.total_task;";
    $department=DB::select($sql);

    return response()->json($department);

  }


public function all_department_count(Request $request, $id)
{
    $query = DB::table('tbl_lead as a')
        ->join('tbl_users as b', 'a.project', '=', 'b.id')
        ->where('a.status', 3);

    /**
     * 🔥 Agar user ID 1 nahi hai to filter lagao
     * ID = 1 → sab data (admin)
     */
    if ($id != 1) {
        $query->where('b.lead_by', $id);
    }

    $department = $query->select(
            DB::raw('COUNT(a.id) as countID'),
            'b.userid',
            'b.total_task',
            'a.project'
        )
        ->groupBy('a.project', 'b.userid', 'b.total_task')
        ->get();

    return response()->json([
        'status' => true,
        'data' => $department
    ]);
}


  public function all_agent_list_data(Request $request){

    $agents = Login::select('id', 'name')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent')
        ->orderby('name', 'ASC')
        ->get();
        
    // $count = Login::select('id', 'name')
    //     ->WHERE('is_deleted', '0')
    //     ->WHERE('user_type', 'agent')
    //     ->orderby('name', 'ASC')
    //     ->count();
        
    
    // $data = array(
    //   "msg"=>'status updated sucessfully!!!',
    //   "status" =>200,
    //   "data" =>  $chatsread
    // //   "isRead" => $chatsread->is_read
    // );
   
    return response()->json($agents); 

  }

  public function all_agent_list_data_admin($id, Request $request){
    

  
    $agents = Login::select('id', 'name')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent')
        ->WHERE('leads_by', $id)
        ->orderby('id', 'DESC')
        ->get();
        
       
    return response()->json($agents); 

  }
  
  public function all_agent_list_data_project($id, Request $request){
      
    $get_lead = Login::select('lead_by')->WHERE('id', $id)->first();
    $get_lead_id = $get_lead->lead_by;

    $agents = Login::select('id', 'name')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent')
        ->WHERE('lead_by', $get_lead_id)
        ->orderby('id', 'DESC')
        ->get();
   
      return response()->json($agents); 

  }
  
  public function all_agent_list_project($id, Request $request){
      
      $get_lead = Login::select('lead_by')->WHERE('id', $id)->first();
    $get_lead_id = $get_lead->lead_by;

    $agents = Login::select('*')
        ->WHERE('is_deleted', '0')
        ->WHERE('user_type', 'agent')
        ->orderby('id', 'DESC')
        ->WHERE('lead_by', $get_lead_id)
        ->get();
    return response()->json($agents); 

  }
  
  public function skills_details($id, Request $request){
  $sql = "SELECT id, name FROM `tbl_skills` WHERE 1 AND id IN ($id)";
  $assinee=DB::select($sql);
  return response()->json($assinee);
}
public function emp_details($id, Request $request)
{
  $sql = "SELECT * FROM tbl_employees WHERE employee_id ='$id'";
  $assinee=DB::select($sql);
  return response()->json($assinee);
}
public function addModule(request $request){
      try
      {
              $validate = $request->validate([
                     'project_id'=>'required',
                     'module_name'=>'required',
                     'start_date'=>'required',
                     'end_date'=>'required'
                  ]);
              if($validate){
                  $data = [
                        'project_id'=>$request->project_id,
                        'module_name'=>$request->module_name,
                        'start_date'=>$request->start_date,
                        'end_date'=>$request->end_date
                      ];
                  $insertData = DB::table('module')->insert($data); 
                   return response([
                        'status' => "success",
                        'statuscode' => 200,
                        'message' => __('data save successfully'),
                        'data' =>$data
                    ], 200);
              }
      }catch(Exception $e){
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getmessage()], 500);
      }
  }
   public function read_module_all(request $request) {
       
        $project_id = $request->project_id;
        $department = TaskModules::where('project_id', $project_id)->get();
        return response([
          'status' => "success",
          'statuscode' => 200,
          'message' => __('Modules Search Results'),
          'data' =>$department
      ], 200);
  }
  
  
  
public function readModule(request $request)
{
    try{
          
          $project_id =  $request->project_id;
          $keywords =  $request->keywords;
          $paginateValue = $request->paginate;
          if(!empty($project_id))
          {
              $data = DB::table('module')->where('project_id',$project_id)->paginate(12);
              return response([
                            'status' => "success",
                            'statuscode' => 200,
                            'message' => __('Modules Search Results'),
                            'data' =>$data
                        ], 200);
           }                
          
          elseif(!empty($keywords))
          {
              
              $data = DB::table('module')->where('module_name','like',$keywords)->orWHERE('start_date','like',$keywords)->orWHERE('end_date',$keywords)->paginate(12);
              return response([
                            'status' => "success",
                            'statuscode' => 200,
                            'message' => __('Modules Search Results'),
                            'data' =>$data
                        ], 200);
           if(!empty($paginateValue))
           {
                dd('hello3');
               $data = DB::table('module')->where('module_name','like',$keywords)->orWHERE('start_date','like',$keywords)->orWHERE('end_date',$keywords)->paginate($paginateValue);
              return response([
                            'status' => "success",
                            'statuscode' => 200,
                            'message' => __('Modules Search Results'),
                            'data' =>$data
                        ], 200);
           }  
            
          }
          else
          {
              if(!empty($paginateValue)){
               
               $data =  DB::table('module')->paginate($paginateValue);
               return response([
                            'status' => "success",
                            'statuscode' => 200,
                            'message' => __('Modules Details'),
                            'data' =>$data
                        ], 200);
           }else
          {  
              $data =  DB::table('module')->paginate(12);
               return response([
                            'status' => "success",
                            'statuscode' => 200,
                            'message' => __('Modules Details'),
                            'data' =>$data
                        ], 200);
           }
                        
          }
    }catch(Exception $e){
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getmessage()], 500);
      }
}


public function SearchModule(request $request)
{
    
    try{
          
          $project_id =  $request->project_id;
          $keywords =  $request->keywords;
          $paginateValue = $request->paginate;
          if(!empty($keywords) && !empty($project_id))
          {
              
              $data = DB::table('module')->where('module_name',$keywords)->where('project_id',$project_id)->orWHERE('start_date','like',$keywords)->orWHERE('end_date',$keywords)->paginate(12);
              return response([
                            'status' => "success",
                            'statuscode' => 200,
                            'message' => __('Modules Search Results'),
                            'data' =>$data
                        ], 200);
          }
    }
    catch(Exception $e)
    {
          return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getmessage()], 500);
    }
}


public function updateModuleData($id)
{
    try{
          $getData = DB::table('module')->where('id',$id)->first();
          return response([
                        'status' => "success",
                        'statuscode' => 200,
                        'message' => __('data'),
                        'data' =>$getData
                    ], 200);
    }catch(Exception $e){
          return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getmessage()], 500);
    }
}
public function updateModule(request $request,$id){
       
      try
      {
          
          $validate = $request->validate([
                     'module_name'=>'required',
                     'start_date'=>'required',
                     'end_date'=>'required'
                  ]);
       
          if($validate){
                  $data = [
                            'module_name'=>$request->module_name,
                            'start_date'=>$request->start_date,
                            'end_date'=>$request->end_date
                      ];
                           
            $updateData = DB::table('module')->where('id',$id)->update($data);  
            $updatedData = DB::table('module')->where('id',$id)->first();
                           return response([
                                'status' => "success",
                                'statuscode' => 200,
                                'message' => __('data update successfully'),
                                'data' => $updatedData
                            ], 200);
              }
      }catch(Exception $e){
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getmessage()], 500);
      }
  }
  
  
public function deleteModule($id)
{
    try{
          $deleteData = DB::table('module')->where('id',$id)->delete();
          $data =  DB::table('module')->get();
           return response([
                        'status' => "success",
                        'statuscode' => 200,
                        'message' => __('Modules Deleted'),
                        'data' =>$data
                    ], 200);
    }catch(Exception $e){
           return response(['status' => "error", 'statuscode' => 500, 'message' => $e->getmessage()], 500);
      }
}  



public function update_task_module($id, Request $request){
    $request->validate([
        'module_name' => 'required',
        'start_date' => 'required',
        'end_date' => 'required'
    ]);
    
    
    $dataUpd = [
                'module_name'=>$request->module_name,
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date
          ];
                           
    $updateData = DB::table('module')->where('id',$id)->update($dataUpd);
    
    
    $data = "Data Update Successfully!!!";
    return response()->json($data); 
  }
  
  
  public function all_agent_chat_group($id, Request $request){
      
      
    $keyword = $id;

    $data = DB::table('tbl_users')
            ->WHERE('user_type', 'project')
            ->whereRaw('FIND_IN_SET(?, assignee) > 0', [$keyword])
            ->orderBy('name', 'ASC')
            ->get();
            
    return response()->json($data); 
      
  }

  
}
