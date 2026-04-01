<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller{
  public function index(){
   
  }

  public function add_template(Request $request){

    
    $request->validate([
        'name' => 'required',
        'template_id' => 'required',
        'message' => 'required'
    ]);

    $name_exists = Template::WHERE('name', $request->name)->first();
    if($name_exists){
      $result = "<span class='text-danger'>Name Already Exists. You can not save Template Name!!!</span>";
    } else {

      // Url Encode
      $urlEncode = urlencode($request->message) . "\n";

      $source = new template;
      $source->name = $request->name;
      $source->message = $request->message;
      $source->uni_code_msg = $request->uni_code_msg;
      $source->template_id = $request->template_id;
      $source->is_deleted = '0';
      $source->save();
      $result = "Data Store Successfully!!!";
    }
  
    return response()->json($result);

  }

  public function template_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $sources = Template::latest()
                ->skip($offset)
                ->take(12)
                ->WHERE('is_deleted', '0')
                ->get();

    $sources_count = Template::All()
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

  public function edit_template($id){
    $source = Template::where('id', $id)
             ->first();
    return response()->json($source); 
  }

  public function update_template($id, Request $request){
    $request->validate([
        'name' => 'required',
        'template_id' => 'required',
        'message' => 'required'
    ]);

    $name_exists = Template::WHERE('name', $request->name)->WHERE('id', '!=', $id)->first();
    if($name_exists){
      $data = "<span class='text-danger'>Name Already Exists. You can not save Template Name!!!</span>";
    } else {

      $urlEncode = urlencode($request->message) . "\n";

      $source = new Template;
      $source = Template::find($id);
      $source->template_id = $request->template_id;
      $source->message = $request->message;
      $source->uni_code_msg = $request->uni_code_msg;;
      $source->name = $request->name;
      $source->save();
      $data = "Data Update Successfully!!!";
    }
    return response()->json($data); 
  }



  public function template_delete($id){

    $source = Template::find($id);
    $source->is_deleted = 1;
    $source->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  public function all_template_list(Request $request){

    $sources = Template::latest()
                ->WHERE('is_deleted', '0')
                ->get();
    return response()->json($sources); 

  }

  public function all_template_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name FROM `tbl_lead` a, tbl_source b WHERE 1 AND a.source = b.id GROUP BY a.source, b.name;";
    $source=DB::select($sql);
    return response()->json($source); 

  }



  
}
