<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller{
  public function index(){
   
  }

  public function add_category(Request $request){

    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = Category::WHERE('name', $request->name)->first();
    if($name_exists){
      $result = "<span class='text-danger'>Name Already Exists. You can not save Category Name!!!</span>";
    } else {

      $category = new Category;
      $category->name = $request->name;
      $category->status = $request->status;
      $category->is_deleted = '0';
      $category->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function category_list(Request $request){

    $page = $request->get('page');
    $keywords = $request->get('keywords');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $categorys = category::latest()
                ->skip($offset)
                ->take(12)
                ->WHERE('is_deleted', '0')
                ->get();
    if(!empty($keywords))
    {
        $categorys =category::where('name','like','%'.$keywords.'%')->orWhere('status','like','%'.$keywords.'%')->take(12)->where('is_deleted','0')->get();
    }
    $categorys_count = Category::All()
            ->WHERE('is_deleted', '0');

    $categorys_count = $categorys_count->count();

    $data = array(
      "data" => $categorys,
      "total" => $categorys_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_category($id){
    $category = Category::where('id', $id)
             ->first();
    return response()->json($category); 
  }

  public function update_category($id, Request $request){
    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = Category::WHERE('name', $request->name)->WHERE('id', '!=', $id)->first();
    if($name_exists){
      $data = "<span class='text-danger'>Name Already Exists. You can not save Category Name!!!</span>";
    } else {
      $category = new Category;
      $category = Category::find($id);
      $category->status = $request->status;
      $category->name = $request->name;
      $category->save();
      $data = "Data Update Successfully!!!";
    }
    return response()->json($data); 
  }


  

  public function category_delete($id){

    $category = Category::find($id);
    $category->is_deleted = 1;
    $category->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  public function all_category_list(Request $request){

    $categorys = Category::latest()
                ->WHERE('is_deleted', '0')
                ->get();
    return response()->json($categorys); 

  }

  public function all_category_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name, a.category FROM `tbl_lead` a, tbl_category b WHERE 1 AND a.category = b.id GROUP BY a.category, b.name;";
    $category=DB::select($sql);
    return response()->json($category); 

  }

  public function all_status_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name, a.status FROM `tbl_lead` a, tbl_source b WHERE 1 AND a.status = b.id GROUP BY a.status, b.name;";
    $category=DB::select($sql);
    return response()->json($category); 

  }
  
  public function all_status_count_project($id, Request $request){

    $sql = "SELECT count(a.id) as countID, b.name FROM `tbl_lead` a, tbl_source b WHERE 1 AND a.project = '".$id."' AND a.status = b.id GROUP BY a.status, b.name;";
    $category=DB::select($sql);
    return response()->json($category); 

  }

  



  
}
