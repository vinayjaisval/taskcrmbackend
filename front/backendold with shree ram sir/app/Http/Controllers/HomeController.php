<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
//use Mail;

use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller{
    public function index(){
       
    }
  
    public function mail_send(){
        
        Mail::to('skp@skylabstech.com')->send(new MyMail());
        echo "Report Sent Successfully";
          
    }
  
  
 
  


  
}
