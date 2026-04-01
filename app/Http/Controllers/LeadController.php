<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Login;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Carbon\Carbon;

use Mail;

class LeadController extends Controller{
  public function index(){
   
  }

  public function add_lead($id, Request $request){

    $request->validate([
        'name' => 'required',
        'status' => 'required'
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

    // Check Total Time Calculation...
    $dateTimeObject1 = date_create(date('Y-m-d H:i:s', strtotime($request->start_task))); 
    $dateTimeObject2 = date_create(date('Y-m-d H:i:s', strtotime($request->dedline)));
    $interval = date_diff($dateTimeObject1, $dateTimeObject2);
    $min = $interval->days * 24 * 60;
    $min += $interval->h * 60;
    $min += $interval->i;
    
    
    $lotalDays = $interval->days;
    $totaltimededuct=0;
    if($lotalDays >= 1){
        $totaltimededuct = (900 * $lotalDays);
    }
    $finalminustes =  ($min-$totaltimededuct);
    
    
    

    $lead = new Lead;
    $lead->name = $request->name;
    $lead->status = $request->status;
    $lead->category = $request->category;
    $lead->start_task = $request->start_task;
    $lead->dedline = $request->dedline;
    $lead->assignee = $assignees;
    $lead->remarks = $request->remarks;
    $lead->project = $request->project;
    $lead->module = $request->module;
    $lead->added_by = $id;
    $lead->tot_assignee = $countAssignee;
    $lead->total_time_assign = $finalminustes;
    $lead->is_deleted = '0';
    $lead->save();
    $result = "Data Store Successfully!!!";
  
    return response()->json($result);

  }

  public function timer_calculation($id, $lead){

    $leadData = DB::table('tbl_lead')
            ->WHERE('id', $lead)
            ->first();

    $time_status = $leadData->time_status;
    $last_time_update = $leadData->last_time_update;
    $total_working_time = $leadData->total_working_time;
    $curDateTime = date('Y-m-d H:i:s');

    // Check Total Time Calculation...
    $dateTimeObject1 = date_create(date('Y-m-d H:i:s', strtotime($last_time_update))); 
    $dateTimeObject2 = date_create(date('Y-m-d H:i:s', strtotime($curDateTime)));
    $interval = date_diff($dateTimeObject1, $dateTimeObject2);
    $min = $interval->days * 24 * 60;
    $min += $interval->h * 60;
    $min += $interval->i;
    
    $new_total_working_time = ($min + $total_working_time);
    
    $leadUpd = new Lead;
    $leadUpd = Lead::find($lead);
    if($time_status == 1){
      $leadUpd->total_working_time = $new_total_working_time;
      $leadUpd->time_status = 0;
    } else {
      $leadUpd->time_status = 1;
    }
    $leadUpd->last_time_update = date('Y-m-d H:i:s');
    $leadUpd->save();

    if($time_status == 1){
      $time_status = 0;
      $min = $min;
    } else {
      $time_status = 1;
      $min = 0;
    }

    // Check Lead Assign in follow Time
    $leadDataFollow = DB::table('tbl_lead_follow_timer')
            ->WHERE('lead_id', $lead)
            ->WHERE('user_id', $id)
            ->orderBy('id', 'DESC')
            ->first();
    if($leadDataFollow == null){
      // Insert
      $affectedRows = DB::table('tbl_lead_follow_timer')
        ->insert([
            'user_id' => $id,
            'lead_id' => $lead,
            'updatetime' => date('Y-m-d H:i:s'),
            'status' => $time_status,
            'working_time' => $min,
            'total_working_time' => $new_total_working_time,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    } else {
      if($leadDataFollow->updatetimes == null){
        // Update
        $affectedRows = DB::table('tbl_lead_follow_timer')
        ->WHERE('id', $leadDataFollow->id)
        ->update([
            'user_id' => $id,
            'lead_id' => $lead,
            'updatetimes' => date('Y-m-d H:i:s'),
            'status' => $time_status,
            'working_time' => $min,
            'total_working_time' => $new_total_working_time,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
      } else {
        // Insert
        $affectedRows = DB::table('tbl_lead_follow_timer')
        ->insert([
            'user_id' => $id,
            'lead_id' => $lead,
            'updatetime' => date('Y-m-d H:i:s'),
            'status' => $time_status,
            'working_time' => $min,
            'total_working_time' => $new_total_working_time,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
      }
    }


    

  
    $data = "Data Update Successfully!!!";
    return response()->json($data); 

  }

  public function lead_list($id, Request $request){
    
    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $keywords = $request->get('keywords');
    
    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name',
        'tbl_users.name as user_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assignee')
      ->WHERE('tbl_lead.is_deleted', '0');
      if($user_type == 'agent'){
        $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads = $leads->WHERE('tbl_lead.added_by', $id);
      } 
      
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.updated_at', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0');
      if($user_type == 'agent'){
        $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
      }
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }
  
  
  
  public function leads_project($id, Request $request){


    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $keywords = $request->get('keywords');
    
    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name',
        'tbl_users.name as user_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assignee')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id);
      
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.updated_at', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id);
    
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function leads_users_list($id, Request $request){

   

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $keywords = $request->get('keywords');
    
    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name',
        'tbl_users.name as user_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assignee')
      ->WHERE('tbl_lead.is_deleted', '0');
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0');
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
     
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }
  
  public function leads_projects_list($id, Request $request){

  

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $keywords = $request->get('keywords');
    
    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name',
        'tbl_users.name as user_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assignee')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id);
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id);
     
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }
  
  
  public function leads_category_list($id, Request $request){

   

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $keywords = $request->get('keywords');
    
    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name',
        'tbl_users.name as user_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assignee')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.category', $id);
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.category', $id);
     
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }
  
  public function leads_status_list($id, Request $request){

  

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $keywords = $request->get('keywords');
    
    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name',
        'tbl_users.name as user_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assignee')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.status', $id);
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.status', $id);
     
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }
  
  
  public function leads_users_list_project($id, $sess_id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $keywords = $request->get('keywords');
    
    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name',
        'tbl_users.name as user_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assignee')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $sess_id);
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $sess_id);
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
     
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }


  public function leads_todo($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.status', '!=', '3');

      if($user_type == 'agent'){
        $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads = $leads->WHERE('tbl_lead.added_by', $id);
      } 
      
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.status', '!=', '3');
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');

      if($user_type == 'agent'){
        $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }
  
  
  public function leads_todo_project($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

 
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id)
      ->WHERE('tbl_lead.status', '!=', '3');

      

     
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id)
      ->WHERE('tbl_lead.status', '!=', '3');
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');

    
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }
  
  

  public function leads_group($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.tot_assignee', '>', '1'); 
     
      if($user_type == 'agent'){
        $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads = $leads->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.tot_assignee', '>', '1'); 

    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }
  
  
  public function leads_group_project($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

 
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.project', $id)
      ->WHERE('tbl_lead.tot_assignee', '>', '1'); 
     
     
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id)
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.tot_assignee', '>', '1'); 

   
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }

  public function leads_pending($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '2'); 

      if($user_type == 'agent'){
        $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads = $leads->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '2');
      
      if($user_type == 'agent'){
        $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }
  
  public function leads_pending_project($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '2')
      ->WHERE('tbl_lead.project', $id); 

      
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '2')
      ->WHERE('tbl_lead.project', $id);
      
     
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }

  public function leads_inprogress($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '1'); 
      
      if($user_type == 'agent'){
        $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads = $leads->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '1');
      
      if($user_type == 'agent'){
        $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }
  
  public function leads_inprogress_project($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '1')
      ->WHERE('tbl_lead.project', $id); 
      
      
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '1')
      ->WHERE('tbl_lead.project', $id);
      
     
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
    

  }


  public function leads_completed($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '3'); 
    
      if($user_type == 'agent'){
        $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads = $leads->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '3');
      
      if($user_type == 'agent'){
        $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
  
  }
  
  public function leads_completed_project($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '3')
      ->WHERE('tbl_lead.project', $id); 
    
      
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '3')
      ->WHERE('tbl_lead.project', $id);
      
      
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
  
  }

  public function leads_due($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
    

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status','!=', '3'); 
     
      if($user_type == 'agent'){
        $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads = $leads->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '!=', '3');
     
      if($user_type == 'agent'){
        $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
      } 

      if($user_type == 'admin'){
        $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
      } 
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
  
  }
  
  public function leads_due_project($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

   

    $leads = Lead::select(
        'tbl_lead.id',
        'tbl_lead.name',
        'tbl_lead.dedline',
        'tbl_lead.assignee',
        'tbl_lead.remarks',
        'tbl_source.name as source_name',
        'tbl_category.name as category_id_name'
      )
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id)
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status','!=', '3'); 
     
     
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.project', $id)
      //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
      ->WHERE('tbl_lead.status', '!=', '3');
     
      
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 
  
  }

  public function lead_list_assign(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $leads = Lead::orderby('tbl_lead.id', 'DESC')
                ->select('tbl_lead.id', 'tbl_lead.name', 'tbl_lead.email', 'tbl_lead.phone', 'tbl_users.name as assigned_to')
                ->skip($offset)
                ->take(12)
                ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assign_to')
                ->WHERE('tbl_lead.is_deleted', '0')
                ->WHERE('tbl_lead.assign_to', '>', '0')
                ->WHERE('tbl_lead.type', 'Student');
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
        $query->orWhere('tbl_lead.name', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_users.name', 'like', '%'.$keywords.'%');
        });
    }
    $leads = $leads->get();

    $leads_count = Lead::orderby('tbl_lead.id', 'DESC')
        ->select('tbl_lead.id')
        ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead.assign_to')
        ->WHERE('tbl_lead.is_deleted', '0')
        ->WHERE('tbl_lead.assign_to', '>', '0')
        ->WHERE('tbl_lead.type', 'Student');
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
      $query->orWhere('tbl_lead.name', 'like', '%'.$keywords.'%')
        ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
        ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
        ->orWHERE('tbl_users.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function leads_to_assign(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school','tbl_source.name as source_name')
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.assign_to', '0')
      ->WHERE('tbl_lead.type', 'Student');
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.assign_to', '0')
      ->WHERE('tbl_lead.type', 'Student');
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_lead($id){
    $lead = Lead::where('id', $id)
             ->first();
    return response()->json($lead); 
  }

  public function update_lead($id, Request $request){
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

    // Check Total Time Calculation...
    $dateTimeObject1 = date_create(date('Y-m-d H:i:s', strtotime($request->start_task))); 
    $dateTimeObject2 = date_create(date('Y-m-d H:i:s', strtotime($request->dedline)));
    $interval = date_diff($dateTimeObject1, $dateTimeObject2);
    $min = $interval->days * 24 * 60;
    $min += $interval->h * 60;
    $min += $interval->i;
    
    $lotalDays = $interval->days;
    $totaltimededuct=0;
    if($lotalDays >= 1){
        $totaltimededuct = (900 * $lotalDays);
    }
    $finalminustes =  ($min-$totaltimededuct);

    $lead = new Lead;
    $lead = Lead::find($id);
    $lead->name = $request->name;
    $lead->status = $request->status;
    $lead->category = $request->category;
    $lead->start_task = $request->start_task;
    $lead->dedline = $request->dedline;
    $lead->assignee = $assignees;
    $lead->remarks = $request->remarks;
    $lead->tot_assignee = $countAssignee;
    $lead->project = $request->project;
    $lead->module = $request->module;
    $lead->tot_assignee = $countAssignee;
    $lead->total_time_assign = $finalminustes;
    $lead->save();
    $data = "Data Update Successfully!!!";
    return response()->json($data); 
  }


  public function update_lead_followup($id, $user_id, Request $request){

    // == save in database
    $lead = new Lead;
    $lead = Lead::find($id);
    $lead->category = $request->category;
    $lead->status = $request->status;
    $lead->updated_at = date('Y-m-d H:i:s');
    $lead->save();

    // ====
    $affectedRows = DB::table('tbl_lead_follow')
    ->insert([
        'lead_id' => $id,
        'remarks' => $request->newremarks,
        'category' => $request->category,
        'status' => $request->status,
        'created_at' => date('Y-m-d H:i:s'),
        'created_by' => $user_id
    ]);


    $data = "Followup Update Successfully!!!";
    return response()->json($data); 
  }

  public function leadofy_mail_api($api_key, $subject, $emails, $type, $template, $url, $sender_name, $sender_email)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://bulkmail.smsby2.in/api.html');
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $payload = array(
        'api_key' => $api_key,
        'subject' => $subject,
        'sender_name' => $sender_name,
        'sender_email' => $sender_email,
        'emails' => $emails,
        'type' => $type,
        'template' => $template,
        'url' => $url
    );
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    $response = curl_exec($ch);
    curl_close($ch);
}

  public function sendLeadToOEL($data){

      $dataSend = [
        'source' => $data['source'],
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'father_name' => $data['father_name'],
        'subjects' => $data['subjects'],
        'stream' => $data['stream'],
        'country' => $data['country'],
        'state' => $data['state'],
        'pincode' => $data['pincode'],
        'school' => $data['school']
      ];
      $response = Http::post('https://www.overseaseducationlane.com/api/save_lead_from_mycrmdesk', $dataSend);
    
      echo response($response);
  
  }


  

  public function lead_delete($id){

    $lead = Lead::find($id);
    $lead->is_deleted = 1;
    $lead->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  // Agent Lead Here===
  public function add_lead_agent($id, Request $request){

    $request->validate([
        'name' => 'required'
    ]);

    $lead = new Lead;
    $lead->type = $request->type;
    $lead->contact_person = $request->contact_person;
    $lead->source = $request->source;
    $lead->name = $request->name;
    $lead->email = $request->email;
    $lead->phone = $request->phone;
    $lead->father_name = $request->father_name;
    $lead->category_name = $request->category_name;
    $lead->subjects = $request->subjects;
    $lead->stream = $request->stream;
    $lead->country = $request->country;
    $lead->state = $request->state;
    $lead->city = $request->city;
    $lead->pincode = $request->pincode;
    $lead->school = $request->school;
    $lead->intrest = $request->intrest;
    $lead->category = $request->category;
    $lead->remarks = $request->remarks;
    $lead->added_by = $id;
    $lead->assign_to = $id;
    $lead->is_deleted = '0';
    $lead->save();
    $result = "Data Store Successfully!!!";
    return response()->json($result);

  }

  public function lead_list_agent($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school', 'tbl_lead.contact_person', 'tbl_source.name as source_name', 'tbl_category.name as category_id_name')
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', 'Student')
      ->WHERE('tbl_lead.assign_to', $id);
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', 'Student')
      ->WHERE('tbl_lead.assign_to', $id);
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  // Department Lead Here===
  public function add_lead_department($id, Request $request){

    $request->validate([
        'name' => 'required'
    ]);

    $lead = new Lead;
    $lead->type = $request->type;
    $lead->contact_person = $request->contact_person;
    $lead->source = $request->source;
    $lead->name = $request->name;
    $lead->email = $request->email;
    $lead->phone = $request->phone;
    $lead->father_name = $request->father_name;
    $lead->category_name = $request->category_name;
    $lead->subjects = $request->subjects;
    $lead->stream = $request->stream;
    $lead->country = $request->country;
    $lead->state = $request->state;
    $lead->city = $request->city;
    $lead->pincode = $request->pincode;
    $lead->school = $request->school;
    $lead->intrest = $request->intrest;
    $lead->category = $request->category;
    $lead->remarks = $request->remarks;
    $lead->added_by = $id;
    $lead->department = $id;
    $lead->assign_to = '0';
    $lead->is_deleted = '0';
    $lead->save();
    $result = "Data Store Successfully!!!";
    return response()->json($result);

  }

  public function lead_list_department($id, Request $request){
    
    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.school_name','tbl_lead.designation','tbl_lead.standard','tbl_lead.address','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school','tbl_source.name as source_name','tbl_category.name as category_id_name')
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('department', $id)
      ->WHERE('tbl_lead.type', 'Student');
      
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();


    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('department', $id)
      ->WHERE('tbl_lead.type', 'Student');
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }


  public function update_lead_assign($id, Request $request){

    if(!empty($request->assign_to) && !empty($request->no_leads)){

      $category = $request->category;

      if(!empty($category)){
        $affectedRows = DB::table('tbl_lead')
          ->take($request->no_leads)
          ->orderby('id', 'ASC')
          ->WHERE('category', $category)
          ->WHERE('assign_to', '0')
          ->update([
              'assign_to' => $request->assign_to
          ]);
      } else {
        $affectedRows = DB::table('tbl_lead')
          ->take($request->no_leads)
          ->orderby('id', 'ASC')
          ->WHERE('assign_to', '0')
          ->update([
              'assign_to' => $request->assign_to
          ]);
      }

      
      $data = "Lead Assigned Successfully!!!";
    } else {
      $data = "You have to select Assign To & No of Leads!!!";
    }
    

    
    return response()->json($data); 
  }



  public function lead_count(Request $request){
      
     

    $leads_count = Lead::WHERE('is_deleted', '0')->count();


    $leads_count_assign = Lead::WHERE('assign_to', '!=', '0')->WHERE('is_deleted', '0')->count();

    $leads_count_assign_non = Lead::WHERE('assign_to', '0')->WHERE('is_deleted', '0')->count();

    $data = array(
      "leads_count" => $leads_count,
      "leads_count_assign" => $leads_count_assign,
      "leads_count_assign_non" => $leads_count_assign_non
    );
    
    return response()->json($data); 

  }

  public function agent_lead_count($id, Request $request){

    $leads_count_assign = Lead::latest()
            ->WHERE('assign_to', $id)
            ->WHERE('category', '=', null)
            ->WHERE('is_deleted', '0');
    $leads_count_assign = $leads_count_assign->count();
    if($id == 0){
      $leads_count_assign = 0;
    } else {
      $leads_count_assign = $leads_count_assign;
    }
    
    return response()->json($leads_count_assign); 

  }

  public function upload_bulk_lead($id, Request $request){

    $file = $request->file;

    // File Details 
    $filename = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();
    $tempPath = $file->getRealPath();
    $fileSize = $file->getSize();
    //$mimeType = $file->getMimeType();

    // Valid File Extensions
    $valid_extension = array("csv");

    // 2MB in Bytes
    $maxFileSize =  921474836480; //2097152;

    // Check file extension
    if(in_array(strtolower($extension),$valid_extension)){

      // Check file size
      if($fileSize <= $maxFileSize){

        // File upload location
        $imageName = time().'.'.$request->file->getClientOriginalExtension();
        // Public Folder
        $request->file->move(public_path('images'), $imageName);

       
        // Reading file
        $filepath = public_path("images/".$imageName);
        $file = fopen($filepath,"r");

        $importData_arr = array();
        $i = 0;

        while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
           $num = count($filedata);
           
           // Skip first row (Remove below comment if you want to skip the first row)
           if($i == 0){
              $i++;
              continue; 
           }
           for ($c=0; $c < $num; $c++) {
              $importData_arr[$i][] = $filedata [$c];
           }
           $i++;
        }
        fclose($file);

        foreach($importData_arr as $importData){

          if(!empty($importData[3])){

            $lead_already = DB::table('tbl_lead')
              ->where('phone', $importData[3])
              ->first();

            // Get Source Id
            $source = DB::table('tbl_source')
              ->where('name', $importData[0])
              ->first();
            if($source == null){
              $source = new Source;
              $source->name = $importData[0];
              $source->status = 'Active';
              $source->is_deleted = '0';
              $source->save();

              $source_id = $source->id;
            } else {
              $source_id = $source->id;
            }

            // Get Country Id
            $country = DB::table('tbl_country')
              ->where('name', $importData[9])
              ->first();
            if($country == null){
              $country = new Country;
              $country->name = $importData[9];
              $country->status = 'Active';
              $country->is_deleted = '0';
              $country->save();

              $country_id = $country->id;
            } else {
              $country_id = $country->id;
            }


            // Get State Id
            $state = DB::table('tbl_state')
              ->where('name', $importData[10])
              ->where('country_id', $country_id)
              ->first();
            if($state == null){
              $state = new State;
              $state->name = $importData[10];
              $state->country_id = $country_id;
              $state->status = 'Active';
              $state->is_deleted = '0';
              $state->save();

              $state_id = $state->id;
            } else {
              $state_id = $state->id;
            }

             // Get City Id
             $city = DB::table('tbl_city')
             ->where('name', $importData[11])
             ->where('country_id', $country_id)
             ->where('state_id', $state_id)
             ->first();
           if($city == null){
             $city = new City;
             $city->name = $importData[11];
             $city->country_id = $country_id;
             $city->state_id = $state_id;
             $city->status = 'Active';
             $city->is_deleted = '0';
             $city->save();

             $city_id = $city->id;
           } else {
             $city_id = $city->id;
           }

            if($lead_already == NULL){
              $lead = new Lead;
              $lead->type = "Student";
              $lead->source = $source_id;
              $lead->name = $importData[1];
              $lead->email = $importData[2];
              $lead->phone = $importData[3];
              $lead->father_name = $importData[4];
              $lead->category_name = $importData[5];
              $lead->subjects = $importData[6];
              $lead->stream = $importData[7];
              $lead->school = $importData[8];
              $lead->country = $country_id;
              $lead->state = $state_id;
              $lead->city = $city_id;
              $lead->pincode = $importData[12];
              $lead->added_by = $id;
              $lead->is_deleted = '0';
              $lead->assign_to = '0';
              $lead->save();
            }
          }
        }
        $data = "Import Successful.";
        // Unlink File ----
        unlink($filepath);
      }else{
        $data = "File too large. File must be less than 2MB.";
      }
    }else{
      $data = "Invalid File Extension.";
    }
    return response()->json($data);

  }


  public function lead_assign_agent($id, Request $request){
    $myVals = $request->get('myVals');
    if(!empty($myVals)){
      $myVals = explode(",",$myVals);
      foreach($myVals as $ab){
        $leads = DB::table('tbl_lead')
          ->where('id', $ab)
          ->update([
              'assign_to' => $request->agent
            ]);
      }
    } else {
      $leads = DB::table('tbl_lead')
        ->where('assign_to', '0');
        if(!empty($request->source)){
          $leads = $leads->WHERE('source', $request->source);
        }
        if(!empty($request->country)){
          $leads = $leads->WHERE('country', $request->country);
        }
        if(!empty($request->state)){
          $leads = $leads->WHERE('state', $request->state);
        }
        if(!empty($request->city)){
          $leads = $leads->WHERE('city', $request->city);
        }
        if(!empty($request->pincode)){
          $leads = $leads->WHERE('pincode', $request->pincode);
        }
        $leads = $leads->update([
          'assign_to' => $request->agent
        ]);
    }
  
    

    $data = "Lead Assign Update Successfully!!!";
    return response()->json($data);
    
    
  }


  public function lead_list_school(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    $type = $request->get('type');
    if($type == 1){
      $type = "School";
    } else {
      $type = "Coaching Center";
    }
    
    print_r($type);
    die;
    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school','tbl_lead.school_name','tbl_lead.type', 'tbl_lead.contact_person', 'tbl_source.name as source_name', 'tbl_category.name as category_id_name')
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', $type);
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', $type);
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function lead_list_department_school($id, Request $request){
      

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');

    $type = $request->get('type');
    if($type == 1){
      $type = "School";
    } else {
      $type = "Coaching Center";
    }
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school', 'tbl_lead.contact_person', 'tbl_source.name as source_name', 'tbl_category.name as category_id_name')
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', $type)
      ->WHERE('tbl_lead.department', $id);
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', $type)
      ->WHERE('tbl_lead.department', $id);
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }


  public function lead_list_agent_school($id, Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    $type = $request->get('type');
    if($type == 1){
      $type = "School";
    } else {
      $type = "Coaching Center";
    }
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school', 'tbl_lead.contact_person', 'tbl_source.name as source_name', 'tbl_category.name as category_id_name')
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', $type)
      ->WHERE('tbl_lead.assign_to', $id);
    if(!empty($keywords)){
      $leads = $leads->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    
    $leads = $leads->orderby('tbl_lead.id', 'DESC')->get();

    $leads_count = Lead::select('tbl_lead.id')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
      ->WHERE('tbl_lead.type', $type)
      ->WHERE('tbl_lead.assign_to', $id);
    if(!empty($keywords)){
      $leads_count = $leads_count->where(function ($query) use ($keywords) {
          $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.email', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_lead.phone', 'like', '%'.$keywords.'%')
            ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
      });
    }
    $leads_count = $leads_count->count();

    $data = array(
      "data" => $leads,
      "total" => $leads_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function get_sel_assignee($id, Request $request){
    $id = explode(",", $id);
    $all_subject = Login::whereIn('id', $id)->get();
    $data = array(
        "data" => $all_subject
    );
    
    return response()->json($data); 
}

public function task_dashboard_history($id, $status, Request $request){

  if($status == 'status1'){
    $start_time = date('Y-m-d 09:30:00');
    $end_time = date('Y-m-d 11:30:00');
  } else if($status == 'status2'){
    $start_time = date('Y-m-d 11:30:00');
    $end_time = date('Y-m-d 13:00:00');
  } else if($status == 'status3'){
    $start_time = date('Y-m-d 14:00:00');
    $end_time = date('Y-m-d 15:30:00');
  } else if($status == 'status4'){
    $start_time = date('Y-m-d 15:30:00');
    $end_time = date('Y-m-d 17:00:00');
  } else {
    $start_time = date('Y-m-d 17:00:00');
    $end_time = date('Y-m-d 18:30:00');
  }


  $users = DB::select("SELECT c.name, c.total_time_assign FROM `tbl_lead_follow_timer` a, `tbl_users` b, `tbl_lead` c WHERE a.user_id = '".$id."' AND b.id = a.user_id AND c.id = a.lead_id AND ( a.`updatetime` BETWEEN '".$start_time."' AND '".$end_time."' OR a.`updatetimes` BETWEEN '".$start_time."' AND '".$end_time."') LIMIT 0,1");
  /*
  $users = DB::table('tbl_lead_follow_timer')
            ->select(
              'tbl_lead_follow_timer.id',
              'tbl_lead_follow_timer.lead_id',
              'tbl_lead_follow_timer.working_time',
              'tbl_lead_follow_timer.total_working_time',
              'tbl_lead.name',
              'tbl_lead.total_time_assign'
            )
            ->leftJoin('tbl_users', 'tbl_users.id', '=', 'tbl_lead_follow_timer.user_id')
            ->leftJoin('tbl_lead', 'tbl_lead.id', '=', 'tbl_lead_follow_timer.lead_id')
            //->WHERE('tbl_lead_follow_timer.status', 1)
            ->WHERE('tbl_lead_follow_timer.user_id', $id)
            ->WHERE('tbl_lead_follow_timer.updatetime', '>=', $start_time)
            ->WHERE('tbl_lead_follow_timer.updatetimes', '>=', $start_time)
            //->whereBetween(DB::raw('DATE(tbl_lead_follow_timer.created_at)'), [$start_time, $end_time])
            ->get();
    */

      $data = array(
          "data" => $users
      );
      
      return response()->json($data); 
}


public function leads_log($id, Request $request){

  $leads = DB::table('tbl_lead_follow')
      ->select('tbl_lead_follow.remarks', 'tbl_lead_follow.created_at', 'tbl_users.name', 'tbl_source.name as status_name')
      ->WHERE('tbl_lead_follow.lead_id', $id)
      ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_lead_follow.created_by')
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead_follow.status')
      ->orderby('tbl_lead_follow.id', 'DESC')->get();


  $data = array(
    "data" => $leads
  );
  
  return response()->json($data); 

}

// ====== Reports ----------

public function leads_rep_deadline($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3'); 
   
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    ->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3');
   
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_owner($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0');
    //->WHERE('tbl_lead.added_by', $id);
    
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0');
    //->WHERE('tbl_lead.added_by', $id);
    
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_assignee($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0');
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)'); 
   
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0');
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_del_one_day($id, Request $request){

  $today_date = date('Y-m-d', strtotime('-1 days'));

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
    
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_del_two_day($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $today_date = date('Y-m-d', strtotime('-2 days'));

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
   // ->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
   
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_del_three_day($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $today_date = date('Y-m-d', strtotime('-3 days'));

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
   
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
   
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_del_week($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $first_date = date('Y-m-d', strtotime('-1 days'));
  $last_date = date('Y-m-d', strtotime('-7 days'));

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;

  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
   
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
   
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_del_month($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $first_date = date('Y-m-d', strtotime('-1 days'));
  $last_date = date('Y-m-d', strtotime('-30 days'));

  $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $user_type = $user->user_type;
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
   
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
    
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_today($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $today = date('Y-m-d');

  
  $user = DB::table('tbl_users')
  ->WHERE('id', $id)
  ->first();
  $user_type = $user->user_type;
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.updated_at', 'like', '%'.$today.'%');
    
    if($user_type == 'agent'){
      $leads = $leads->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads = $leads->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.updated_at', 'like', '%'.$today.'%');
   
    if($user_type == 'agent'){
      $leads_count = $leads_count->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    } 

    if($user_type == 'admin'){
      $leads_count = $leads_count->WHERE('tbl_lead.added_by', $id);
    } 
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}



// ====== Reports ----------

public function leads_rep_deadline_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    ->WHERE('tbl_lead.project', $id)
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3'); 
   
    
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    ->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.status', '!=', '3');
   
   
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_owner_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

 
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.is_deleted', '0');
    //->WHERE('tbl_lead.added_by', $id);
    
    
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.is_deleted', '0');
    //->WHERE('tbl_lead.added_by', $id);
    
    
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_assignee_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.is_deleted', '0');
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)'); 
   
   
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.is_deleted', '0');
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)');
    
    
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_del_one_day_project($id, Request $request){

  $today_date = date('Y-m-d', strtotime('-1 days'));

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

 
    
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
   
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
   
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_del_two_day_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $today_date = date('Y-m-d', strtotime('-2 days'));

  
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
   // ->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
   
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
   
   
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_del_three_day_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $today_date = date('Y-m-d', strtotime('-3 days'));

  
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
    
   
    
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    ->WHERE('tbl_lead.project', $id)
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', 'like', '%'.$today_date.'%'); 
   
  
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_del_week_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $first_date = date('Y-m-d', strtotime('-1 days'));
  $last_date = date('Y-m-d', strtotime('-7 days'));

  

  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
   
   
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    ->WHERE('tbl_lead.project', $id)
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
   
    
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

public function leads_rep_del_month_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $first_date = date('Y-m-d', strtotime('-1 days'));
  $last_date = date('Y-m-d', strtotime('-30 days'));

  
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
   
    
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.dedline', '<=', $first_date.' 23:59')
    ->WHERE('tbl_lead.dedline', '>=', $last_date.' 00:00');
    
    
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}


public function leads_rep_today_project($id, Request $request){

  $page = $request->get('page');
  if($page == 1){
    $offset = 0;
  } else {
    $offset = (($page-1) * 12);
  }
  $keywords = $request->get('keywords');

  $today = date('Y-m-d');

  
  
  

  $leads = Lead::select(
      'tbl_lead.id',
      'tbl_lead.name',
      'tbl_lead.dedline',
      'tbl_lead.assignee',
      'tbl_lead.remarks',
      'tbl_source.name as source_name',
      'tbl_category.name as category_id_name'
    )
    ->skip($offset)
    ->take(12)
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.updated_at', 'like', '%'.$today.'%');
    
   
  if(!empty($keywords)){
    $leads = $leads->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  
  $leads = $leads->orderby('tbl_lead.dedline', 'DESC')->get();

  $leads_count = Lead::select('tbl_lead.id')
    ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.status')
    ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
    ->WHERE('tbl_lead.is_deleted', '0')
    //->whereRaw('FIND_IN_SET("'.$id.'",tbl_lead.assignee)')
    ->WHERE('tbl_lead.status', '!=', '3')
    ->WHERE('tbl_lead.project', $id)
    ->WHERE('tbl_lead.updated_at', 'like', '%'.$today.'%');
   
   
  if(!empty($keywords)){
    $leads_count = $leads_count->where(function ($query) use ($keywords) {
        $query->WHERE('tbl_lead.name', 'like', '%'.$keywords.'%')
          ->orWHERE('tbl_category.name', 'like', '%'.$keywords.'%');
    });
  }
  $leads_count = $leads_count->count();

  $data = array(
    "data" => $leads,
    "total" => $leads_count,
    "per_page" => 12,
    "page" => $page,
    "offset" => $offset
  );
  
  return response()->json($data); 

}

//=== Report #END

public function get_to_task_count($id, Request $request){
    $sql = "SELECT count(a.id) as countID, b.userid, b.total_task FROM `tbl_lead` a, tbl_users b WHERE 1 AND FIND_IN_SET($id, assignee) GROUP BY a.project, b.userid, b.total_task";
    $department=DB::select($sql);
    return response()->json($department);

}


public function assignee_details($id, Request $request){
  $sql = "SELECT id, name FROM `tbl_users` WHERE 1 AND id IN ($id)";
  $assinee=DB::select($sql);
  return response()->json($assinee);
}


public function get_user_tot_task($id, Request $request){
    $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND FIND_IN_SET($id, assignee)";
    $department=DB::select($sql);
    return response()->json($department);
}

public function get_user_tot_delay_task($id, Request $request){ 
  $curDate = date('Y-m-d H:i:s');
  $sql = "SELECT count(id) as countID FROM `tbl_lead` WHERE 1 AND FIND_IN_SET($id, assignee) AND dedline > '".$curDate."' AND status != '3'";
  $department=DB::select($sql);
  
  return response()->json($department);
}

public function get_sel_assignee_by_skills($id, Request $request){
    
 
    $id = str_replace(",","|",$id);
    
    
    $sql = 'SELECT id, name FROM tbl_users WHERE user_type ="agent" and is_deleted = "0" and CONCAT(",", skills, ",") REGEXP ",('.$id.'),"';
    
    $department=DB::select($sql);
   
    
    $data = array(
            "data" => $department
        );
  
    return response()->json($data); 
}

public function get_sel_assignee_by_project($id, Request $request){
    
    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $skills = $user->skills;
    
    if($skills == ""){
        $department=[];
    } else {
        $skills = str_replace(",","|",$skills);
        $sql = 'SELECT id, name FROM tbl_users WHERE user_type ="agent" and is_deleted = "0" and CONCAT(",", skills, ",") REGEXP ",('.$skills.'),"';
        $department=DB::select($sql);
    }
   
    
    $data = array(
            "data" => $department
        );
  
    return response()->json($data);
    
}

public function get_sel_assignee_by_project_team_lead($id, Request $request){
    
    $user = DB::table('tbl_users')
            ->WHERE('id', $id)
            ->first();
    $assignee = $user->assignee;
    if($assignee != ""){
        $sql = "SELECT id, name FROM `tbl_users` WHERE 1 AND id IN ($assignee)";
        $assinee=DB::select($sql);
    } else {
        $assinee=[];
    }
    return response()->json($assinee);
    
}
  


  
}
