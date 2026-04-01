<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SourceController extends Controller{
  public function index(){
   
  }

  public function add_source(Request $request){

    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = Source::WHERE('name', $request->name)->first();
    if($name_exists){
      $result = "<span class='text-danger'>Name Already Exists. You can not save Source Name!!!</span>";
    } else {

      $source = new Source;
      $source->name = $request->name;
      $source->status = $request->status;
      $source->is_deleted = '0';
      $source->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function source_list(Request $request){

    $page = $request->get('page');
    $keywords = $request->get('keywords');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }
   
    $sources = Source::latest()
                ->skip($offset)
                ->take(12)
                ->WHERE('is_deleted', '0')
                ->get();
     if(!empty($keywords)){
      $sources = Source::where('name', 'like', '%'.$keywords.'%')
              ->orWHERE('status', 'like','%'.$keywords.'%')->paginate(12);
    }
    $sources_count = Source::All()
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

  public function edit_source($id){
    $source = Source::where('id', $id)
             ->first();
    return response()->json($source); 
  }

  public function update_source($id, Request $request){
    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = Source::WHERE('name', $request->name)->WHERE('id', '!=', $id)->first();
    if($name_exists){
      $data = "<span class='text-danger'>Name Already Exists. You can not save Source Name!!!</span>";
    } else {
      $source = new Source;
      $source = Source::find($id);
      $source->status = $request->status;
      $source->name = $request->name;
      $source->save();
      $data = "Data Update Successfully!!!";
    }
    return response()->json($data); 
  }


  

  public function source_delete($id){

    $source = Source::find($id);
    $source->is_deleted = 1;
    $source->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  public function all_source_list(Request $request){

    $sources = Source::latest()
                ->WHERE('is_deleted', '0')
                ->get();
    return response()->json($sources); 

  }

  public function all_source_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name FROM `tbl_lead` a, tbl_source b WHERE 1 AND a.source = b.id GROUP BY a.source, b.name;";
    $source=DB::select($sql);
    return response()->json($source); 

  }



  
}
