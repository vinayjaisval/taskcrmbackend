<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller{
  public function index(){
   
  }

  public function start_chat($id, $memId, Request $request){
      
      $is_group = $request->get('is_group');
      if($is_group == 1){
          $group_id = $memId;
      } else {
          $group_id = 0;
      }


    $chat = new Chats;
    $chat->message = $request->message;
    $chat->memb_id = $id;
    $chat->chat_whome = $memId;
    $chat->group_id = $group_id;
    $chat->is_deleted = '0';
    
    if($request->file('file'))
    {
      $file= $request->file('file');
      $filename= date('YmdHi').$file->getClientOriginalName();
      $file->move(public_path('uploads'), $filename);
      $chat->image = $filename;
    }
    
    
    $chat->save();
    $result = "Data Store Successfully!!!";
    return response()->json($result);

  }

  public function chat_list_history($id, $memb, Request $request){
      

    $chats = Chats::select(
            'tbl_users.name as mem_name',
            'tbl_chat.id',
            'tbl_chat.message',
            'tbl_chat.image',
            'tbl_chat.created_at'
        )
        ->orderBy('tbl_chat.id', 'ASC')
        ->WHERE('tbl_chat.memb_id', $id)
        ->WHERE('tbl_chat.chat_whome', $memb)
        ->orWhere('tbl_chat.chat_whome', $id)
        ->WHERE('tbl_chat.memb_id', $memb)
        ->WHERE('tbl_chat.is_deleted', '0')
        ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_chat.memb_id')
        ->get();
   


    $data = array(
      "data" => $chats
    );
    
    return response()->json($data); 

  }
  
  
  public function chat_list_history_group($id, $memb, Request $request){
      

    $chats = Chats::select(
            'tbl_users.name as mem_name',
            'tbl_chat.id',
            'tbl_chat.message',
            'tbl_chat.image',
            'tbl_chat.created_at'
        )
        ->orderBy('tbl_chat.id', 'ASC')
        ->WHERE('tbl_chat.group_id', $memb)
        ->WHERE('tbl_chat.is_deleted', '0')
        ->LeftJoin('tbl_users', 'tbl_users.id', 'tbl_chat.memb_id')
        ->get();
   


    $data = array(
      "data" => $chats
    );
    
    return response()->json($data); 

  }

 

  



  
}
