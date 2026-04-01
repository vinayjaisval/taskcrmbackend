<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SenderController extends Controller{
  public function index(){
   
  }

  public function add_sender(Request $request){

    $request->validate([
        'name' => 'required',
        'entity_id' => 'required'
    ]);

    $name_exists = Sender::WHERE('name', $request->name)->first();
    if($name_exists){
      $result = "<span class='text-danger'>Name Already Exists. You can not save Sender Name!!!</span>";
    } else {

      $source = new Sender;
      $source->name = $request->name;
      $source->entity_id = $request->entity_id;
      $source->is_deleted = '0';
      $source->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function sender_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $sources = Sender::latest()
                ->skip($offset)
                ->take(12)
                ->WHERE('is_deleted', '0')
                ->get();

    $sources_count = Sender::All()
            ->WHERE('is_deleted', '0');

    $sources_count = $sources_count->count();

    $data = array(
      "data" => $sources,
      "total" => $sources_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_sender($id){
    $source = Sender::where('id', $id)
             ->first();
    return response()->json($source); 
  }

  public function update_sender($id, Request $request){
    $request->validate([
        'name' => 'required',
        'entity_id' => 'required'
    ]);

    $name_exists = Sender::WHERE('name', $request->name)->WHERE('id', '!=', $id)->first();
    if($name_exists){
      $data = "<span class='text-danger'>Name Already Exists. You can not save Sender Name!!!</span>";
    } else {
      $source = new Sender;
      $source = Sender::find($id);
      $source->entity_id = $request->entity_id;
      $source->name = $request->name;
      $source->save();
      $data = "Data Update Successfully!!!";
    }
    return response()->json($data); 
  }


  

  public function sender_delete($id){

    $source = Sender::find($id);
    $source->is_deleted = 1;
    $source->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  public function all_sender_list(Request $request){

    $sources = Sender::latest()
                ->WHERE('is_deleted', '0')
                ->get();
    return response()->json($sources); 

  }

  public function all_sender_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name FROM `tbl_lead` a, tbl_sender b WHERE 1 AND a.sender = b.id GROUP BY a.sender, b.name;";
    $source=DB::select($sql);
    return response()->json($source); 

  }



  
}
