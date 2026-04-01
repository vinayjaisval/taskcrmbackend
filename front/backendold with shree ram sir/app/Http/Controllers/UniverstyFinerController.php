<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UniLogin;
use Illuminate\Http\Request;
use DB;

class UniverstyFinerController extends Controller{
  

  // === Login management --------
  public function login(Request $request){
  
    $login = UniLogin::WHERE('is_deleted', '0')
            ->WHERE('phone', $request->userid)
            ->WHERE('password', md5($request->user_pass))
            ->first();
    if($login){
      $login = $login;
    } else {
      $login = "Invalid";
    }
    return response()->json($login); 
  }
  
  
  // Register
  public function register(Request $request){
  
    $request->validate([
        'userid' => 'required',
        'email' => 'required',
        'phone' => 'required'
    ]);

    $userid_exists = UniLogin::WHERE('userid', $request->userid)
            ->first();
    $phone_exists = UniLogin::WHERE('phone', $request->contact_no)
            ->first();
    $email_exists = UniLogin::WHERE('email', $request->email)
            ->first();
    if($userid_exists){
      $result = "User Id Already Exists. You can not Register!!!";
    } else if($phone_exists){
      $result = "Phone No Already Exists. You can not Register!!!";
    } else if($email_exists){
      $result = "Email Id Already Exists. You can not Register!!!";
    } else {
      $curtime = time();
      $api_key =  $curtime ."-" . rand("2222","9999");
      $api_key = md5($api_key);

      $user = new UniLogin;
      $user->name = $request->name;
      $user->userid = $request->userid;
      $user->email = $request->email;
      $user->phone = $request->phone;
      $user->password = md5($request->user_pass);
      $user->api_key = $api_key;
      $user->user_img = '';
      $user->user_type = 'department';
      $user->status = '1';
      $user->is_deleted = '0';
      $user->save();
      $result = "User Registration Successfully!!!";
    }
    return response()->json($result);
  }
  
  public function universty_phone(Request $request){
      $phone_exists = UniLogin::WHERE('phone', $request->userid)
            ->first();
            
    if($phone_exists){
        $login = UniLogin::WHERE('phone', $request->userid)->first();
        if($login){
          $login = $login;
        } else {
          $login = "Invalid";
        }
    } else {
      $curtime = time();
      $api_key =  $curtime ."-" . rand("2222","9999");
      $api_key = md5($api_key);

      $user = new UniLogin;
      $user->name = "";
      $user->userid = "";
      $user->email = "";
      $user->phone = $request->userid;
      $user->password = md5('123');
      $user->api_key = $api_key;
      $user->user_img = '';
      $user->user_type = '';
      $user->status = '1';
      $user->is_deleted = '0';
      $user->save();
      
      // Then login User
      $login = UniLogin::WHERE('phone', $request->userid)->first();
      if($login){
          $login = $login;
      } else {
          $login = "Invalid";
      }
    }
    
    return response()->json($login); 
  }
  
  public function user_details($id){
    $agent = UniLogin::where('id', $id)
             ->first();
    return response()->json($agent); 
  }
  
  public function update_uni_reg($id, Request $request){
      $request->validate([
        'userid' => 'required',
    ]);

    $userid_exists = UniLogin::WHERE('userid', $request->userid)
            ->WHERE('id', '!=', $id)
            ->first();
    $phone_exists = UniLogin::WHERE('phone', $request->phone)
            ->WHERE('id', '!=', $id)
            ->first();
    $email_exists = UniLogin::WHERE('email', $request->email)
            ->WHERE('id', '!=', $id)
            ->first();
    if($userid_exists){
      $data = "User Id Already Exists. You can not Update Agent!!!";
    } else if($phone_exists){
      $data = "Phone No Already Exists. You can not Update Agent!!!";
    } else if($email_exists){
      $data = "Email Id Already Exists. You can not Update Agent!!!";
    } else {

      $agent = new UniLogin;
      $agent = UniLogin::find($id);
      $agent->name = $request->name;
      $agent->phone = $request->phone;
      $agent->email = $request->email;
      $agent->userid = $request->userid;
      $agent->save();
      $data = "Profile Update Successfully!!!";
    }
    return response()->json($data); 
  }
  
  public function universty_apply($id, Request $request){
      
      
      $uni_app = DB::table('tbl_uni_apply')
            ->WHERE('userid', $id)
            ->WHERE('uni_id', $request->uni_id)
            ->first();
            
    if($uni_app == null){
        DB::table('tbl_uni_apply')->insert([
            'userid' => $id,
            'uni_id' => $request->uni_id,
            'uni_name' => $request->uni_name,
            'uni_loc' => $request->uni_loc
         ]);
    }
      
      
     $data = "Applied Successfully!!!";
     return response()->json($data); 
                
                
  }
  
  public function uni_application_list($id, Request $request){
      $uni_app = DB::table('tbl_uni_apply')
        ->WHERE('userid', $id)
        ->get();
    return response()->json($uni_app); 
  }

  
}
