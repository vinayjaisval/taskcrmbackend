<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Login;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Source;
use App\Models\Sender;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;


use Mail;

class LeadController extends Controller{
    
    
    
  public function index(){
   
  }

  public function add_lead($id, Request $request){

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
    $lead->is_deleted = '0';
    $lead->assign_to = '0';
    $lead->save();
    $result = "Data Store Successfully!!!";
    return response()->json($result);

  }

  public function lead_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
    $keywords = $request->get('keywords');
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school','tbl_source.name as source_name','tbl_category.name as category_id_name')
      ->skip($offset)
      ->take(12)
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_category', 'tbl_category.id', 'tbl_lead.category')
      ->WHERE('tbl_lead.is_deleted', '0')
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

    $lead = new Lead;
    $lead = Lead::find($id);
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
    $lead->save();
    $data = "Data Update Successfully!!!";
    return response()->json($data); 
  }


  public function update_lead_followup($id, Request $request){
      
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');
    

    // === Send Notifications/API --------
    // Get Department details
    $department = Login::where('id', $request->department)->first();
    $depEmail = $department->email;
    $depName = $department->name;
   

    // Get Lead Details -- 
    $leadDetails = Lead::where('tbl_lead.id', $id)
      ->select(
          'tbl_lead.id',
          'tbl_lead.name',
          'tbl_lead.phone',
          'tbl_lead.email',
          'tbl_lead.father_name',
          'tbl_lead.category_name',
          'tbl_lead.subjects',
          'tbl_lead.stream',
          'tbl_lead.pincode',
          'tbl_lead.school',
          'tbl_source.name as source_name',
          'tbl_country.name as country_name', 
          'tbl_state.name as state_name', 
          'tbl_city.name as city_name', 
          'tbl_lead.notification',
          'tbl_lead.remarks'
          )
      ->LeftJoin('tbl_source', 'tbl_source.id', 'tbl_lead.source')
      ->LeftJoin('tbl_country', 'tbl_country.id', 'tbl_lead.country')
      ->LeftJoin('tbl_state', 'tbl_state.id', 'tbl_lead.state')
      ->LeftJoin('tbl_city', 'tbl_city.id', 'tbl_lead.city')
      ->first();

    $data = array(
      'source'=>$leadDetails->source_name,
      'name'=>$leadDetails->name,
      'phone'=>$leadDetails->phone,
      'email'=>$leadDetails->email,
      'father_name'=>$leadDetails->father_name,
      'category_name'=>$leadDetails->category_name,
      'subjects'=>$leadDetails->subjects,
      'stream'=>$leadDetails->stream,
      'country'=>$leadDetails->country_name,
      'state'=>$leadDetails->state_name,
      'city'=>$leadDetails->city_name,
      'pincode'=>$leadDetails->pincode,
      'school'=>$leadDetails->school,
      'depName' => $depName,
      'remarks' => $leadDetails->remarks
    );
    if($leadDetails->notification != '1'){
      /*
      Mail::send('mail', $data, function($message) use ($depEmail) {
          $message->to($depEmail, 'Lead From My CRM Desk')->subject('Lead From My CRM Desk');
          $message->from('info@mycrmdesk.com','My CRM Desk');
      });
      */

      $message = "source = " . $leadDetails->source_name .", <br>
      name = " . $leadDetails->name. ", <br>
      phone = " .$leadDetails->phone . ", <br>
      email = ". $leadDetails->email . ", <br>
      father_name = " .$leadDetails->father_name . ", <br> 
      category_name = " .$leadDetails->category_name . ", <br>
      subjects = " .$leadDetails->subjects . ", <br>
      stream = " .$leadDetails->stream . ", <br>
      country = " .$leadDetails->country_name . ", <br> 
      state = " .$leadDetails->state_name . ", <br>
      city = " .$leadDetails->city_name . ", <br>
      pincode = " .$leadDetails->pincode . ", <br>
      school = " .$leadDetails->school . ", <br>
      depName = " . $depName;
    
      if($depEmail == 'info@overseaseducationlane.com'){
        $this->sendLeadToOEL($data);
      } else {
        $this->leadofy_mail_api('YjMxNTQ4MDA3Yzk5MTI3MDkxY2M5YTEwYmNhYzhhNTY=', "Lead From My CRM Desk", $depEmail, '1', $message, '#', "Ekon Academy", "ekon@gmail.com");
      }
    }
    

    // == save in database
    $lead = new Lead;
    $lead = Lead::find($id);
    $lead->assign_to = $request->assign_to;
    $lead->category = $request->category;
    $lead->department = $request->department;
    $lead->remarks = $request->remarks;
    $lead->notification = "1";
    $lead->save();

    // ====
    $affectedRows = DB::table('tbl_lead_follow')
    ->insert([
        'lead_id' => $id,
        'remarks' => $request->remarks,
        'assign_to' => $request->assign_to,
        'category' => $request->category,
        'department' => $request->department,
        'created_at' => date('Y-m-d H:i:s')
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
        'school' => $data['school'],
        'remarks' => $data['remarks']
      ];
      $response = Http::post('https://overseaseducationlane.com/backend/api/save_lead_from_mycrmdesk', $dataSend);
    
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
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school','tbl_source.name as source_name','tbl_category.name as category_id_name')
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
    
    /*
    $lead = new Lead;
    $lead = Lead::find($id);
    $lead->assign_to = $request->assign_to;
    $lead->category = $request->category;
    $lead->department = $request->department;
    $lead->remarks = $request->remarks;
    $lead->save();
    */
    
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
    

    $leads = Lead::select('tbl_lead.id','tbl_lead.name','tbl_lead.phone','tbl_lead.email','tbl_lead.father_name','tbl_lead.category_name','tbl_lead.subjects','tbl_lead.stream','tbl_lead.pincode','tbl_lead.school','tbl_lead.type', 'tbl_lead.contact_person', 'tbl_source.name as source_name', 'tbl_category.name as category_id_name')
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


  public function compose_message($id, Request $request){

    $user="SKYLABSpromo";
    $authkey="92sxixuebx26U";
    $rpt = 1;
    
    // Get Last Limit to Start
    $msgCountNo=0;
    if(!empty($request->category)){
      $sql = "SELECT sum(no_message) as totMessage FROM `tbl_messages` WHERE 1 AND template = '".$request->template."' AND category = '".$request->category."'";
      $msgCount=DB::select($sql);
      foreach($msgCount as $msgCnt){
        $msgCountNo = $msgCnt->totMessage;
      }
    } else {
      $sql = "SELECT sum(no_message) as totMessage FROM `tbl_messages` WHERE 1 AND template = '".$request->template."' ";
      $msgCount=DB::select($sql);
      foreach($msgCount as $msgCnt){
        $msgCountNo = $msgCnt->totMessage;
      }
    }
    if($msgCountNo == null){
      $msgCountNo = 0;
    }
    

  
    // Get Sender 
    $sender = Sender::where('id', $request->sender)->first();
    $sender_name = $sender->name;
    $entityid = $sender->entity_id;

    // Get Template
    $template = Template::where('id', $request->template)->first();
    $text = $template->uni_code_msg;
    $templateid = $template->template_id;

    // Find Mobile No.
    if(!empty($request->phone)){
      $mobile = $request->phone;
    } else if(!empty($request->category)){
      // By category
      $getMobileNo = Lead::where('source', $request->category)->where('is_deleted', 0)->take($request->no_message)->skip($msgCountNo)->orderby('id', 'ASC')->get();
      $mobile = "";
      foreach($getMobileNo as $phons){
        $mobile .= $phons->phone;
        $mobile .= ",";
      }
    } else {
      // By Total No Limit
      $getMobileNo = Lead::where('is_deleted', 0)->take($request->no_message)->skip($msgCountNo)->orderby('id', 'ASC')->get();
      $mobile = "";
      foreach($getMobileNo as $phons){
        $mobile .= $phons->phone;
        $mobile .= ",";
      }
    }
    $mobile = rtrim($mobile, ",");

    /*
    $shree = "
    user-".$user.",
    authkey-".$authkey.",
    sender_name-".$sender_name.",
    mobile-".$mobile.",
    text-".$text.",
    entityid-".$entityid.",
    templateid-".$templateid.",
    ";    
    */
    
    // API For Send SMS---
    $this->send_sms($user, $authkey, $sender_name, $mobile, $text, $entityid, $templateid, $rpt);

    $affectedRows = DB::table('tbl_messages')
    ->insert([
        'sender' => $request->sender,
        'template' => $request->template,
        'category' => $request->category,
        'phone' => $request->phone,
        'no_message' => $request->no_message,
        'status' => 0,
        'created_by' => $id,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $result = "";
  
  
    return response()->json($result);
  }


  function send_sms($user, $authkey, $sender, $mobile, $text, $entityid, $templateid, $rpt){

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://tdots.in/api/pushsms?user={$user}&authkey={$authkey}&sender={$sender}&mobile={$mobile}&text={$text}&entityid={$entityid}&templateid={$templateid}&rpt={$rpt}",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_TIMEOUT => 30000,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        print_r(json_decode($response));
    }

  }


  


  
}
