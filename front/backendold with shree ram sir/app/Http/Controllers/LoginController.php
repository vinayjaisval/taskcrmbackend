<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Login;
use Illuminate\Http\Request;

class LoginController extends Controller{
  public function index(){
    $this->login();
  }

  // === Login management --------
  public function login(Request $request){
  
    $login = Login::WHERE('is_deleted', '0')
            ->WHERE('userid', $request->userid)
            ->WHERE('status', '1')
            ->WHERE('password', md5($request->user_pass))
            ->first();
    if($login){
      $login = $login;
    } else {
      $login = "Invalid";
    }
    return response()->json($login); 
  }

  
}
