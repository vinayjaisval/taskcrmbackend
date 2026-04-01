<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Punch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller{
  public function index(){
   
  }

  public function add_country(Request $request){

    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = Country::WHERE('name', $request->name)->first();
    if($name_exists){
      $result = "<span class='text-danger'>Name Already Exists. You can not save Country Name!!!</span>";
    } else {

      $country = new Country;
      $country->name = $request->name;
      $country->status = $request->status;
      $country->is_deleted = '0';
      $country->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function country_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $countrys = Country::latest()
                ->skip($offset)
                ->take(12)
                ->WHERE('is_deleted', '0')
                ->get();

    $countrys_count = Country::WHERE('is_deleted', '0');

    $countrys_count = $countrys_count->count();

    $data = array(
      "data" => $countrys,
      "total" => $countrys_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_country($id){
    $country = Country::where('id', $id)
             ->first();
    return response()->json($country); 
  }

  public function update_country($id, Request $request){
    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = Country::WHERE('name', $request->name)->WHERE('id', '!=', $id)->first();
    if($name_exists){
      $data = "<span class='text-danger'>Name Already Exists. You can not save Country Name!!!</span>";
    } else {
      $country = new Country;
      $country = Country::find($id);
      $country->status = $request->status;
      $country->name = $request->name;
      $country->save();
      $data = "Data Update Successfully!!!";
    }
    return response()->json($data); 
  }


  

  public function country_delete($id){

    $country = Country::find($id);
    $country->is_deleted = 1;
    $country->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  public function all_country_list(Request $request){

    $countrys = Country::latest()
                ->WHERE('is_deleted', '0')
                ->get();
    return response()->json($countrys); 

  }

  public function all_country_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name FROM `tbl_lead` a, tbl_country b WHERE 1 AND a.country = b.id GROUP BY a.country, b.name;";
    $country=DB::select($sql);
    return response()->json($country); 

  }


  // State -===

  public function add_state(Request $request){

    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = State::WHERE('name', $request->name)->WHERE('country_id', $request->country_id)->first();
    if($name_exists){
      $result = "<span class='text-danger'>Name Already Exists. You can not save State Name!!!</span>";
    } else {

      $state = new State;
      $state->name = $request->name;
      $state->country_id = $request->country_id;
      $state->status = $request->status;
      $state->is_deleted = '0';
      $state->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function state_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $states = State::select('tbl_state.id', 'tbl_state.name', 'tbl_state.status', 'tbl_country.name as country_name')
                ->skip($offset)
                ->take(12)
                ->LeftJoin('tbl_country', 'tbl_country.id', 'tbl_state.country_id')
                ->WHERE('tbl_state.is_deleted', '0');
    if(!empty($keywords)){
      $states = $states->where(function ($query) use ($keywords) {
        $query->where('tbl_state.name', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_country.name', 'like', '%'.$keywords.'%');
        });
    }
    $states =  $states->orderby('tbl_state.id', 'DESC')->get();


    $states_count = State::select('tbl_state.id')
               
                ->LeftJoin('tbl_country', 'tbl_country.id', 'tbl_state.country_id')
                ->WHERE('tbl_state.is_deleted', '0');
    if(!empty($keywords)){
      $states_count = $states_count->where(function ($query) use ($keywords) {
        $query->where('tbl_state.name', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_country.name', 'like', '%'.$keywords.'%');
        });
    }
    $states_count =  $states_count->count();


    $data = array(
      "data" => $states,
      "total" => $states_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_state($id){
    $state = State::where('id', $id)
             ->first();
    return response()->json($state); 
  }

  public function update_state($id, Request $request){
    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = State::WHERE('name', $request->name)->WHERE('country_id', $request->country_id)->WHERE('id', '!=', $id)->first();
    if($name_exists){
      $data = "<span class='text-danger'>Name Already Exists. You can not save State Name!!!</span>";
    } else {
      $state = new State;
      $state = State::find($id);
      $state->status = $request->status;
      $state->name = $request->name;
      $state->country_id = $request->country_id;
      $state->save();
      $data = "Data Update Successfully!!!";
    }
    return response()->json($data); 
  }


  

  public function state_delete($id){

    $state = State::find($id);
    $state->is_deleted = 1;
    $state->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  public function all_state_list(Request $request){

    $country = $request->get('country');

    $states = State::latest()
        ->WHERE('is_deleted', '0');
    if(!empty($country)){
      $states = $states->WHERE('country_id', $country);
    }
    $states = $states->get();
    return response()->json($states); 

  }

  public function all_state_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name FROM `tbl_lead` a, tbl_state b WHERE 1 AND a.state = b.id GROUP BY a.state, b.name;";
    $state=DB::select($sql);
    return response()->json($state); 

  }


  // City -===

  public function add_city(Request $request){

    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = City::WHERE('name', $request->name)->WHERE('country_id', $request->country_id)->WHERE('state_id', $request->state_id)->first();
    if($name_exists){
      $result = "<span class='text-danger'>Name Already Exists. You can not save City Name!!!</span>";
    } else {

      $city = new City;
      $city->name = $request->name;
      $city->country_id = $request->country_id;
      $city->state_id = $request->state_id;
      $city->status = $request->status;
      $city->is_deleted = '0';
      $city->save();
      $result = "Data Store Successfully!!!";
    }
    return response()->json($result);

  }

  public function city_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $citys = City::select('tbl_city.id', 'tbl_city.name', 'tbl_city.status', 'tbl_state.name as state_name', 'tbl_country.name as country_name')
                ->skip($offset)
                ->take(12)
                ->LeftJoin('tbl_country', 'tbl_country.id', 'tbl_city.country_id')
                ->LeftJoin('tbl_state', 'tbl_state.id', 'tbl_city.state_id')
                ->WHERE('tbl_city.is_deleted', '0');
    if(!empty($keywords)){
      $citys = $citys->where(function ($query) use ($keywords) {
        $query->where('tbl_city.name', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_country.name', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_state.name', 'like', '%'.$keywords.'%');
        });
    }
    $citys =  $citys->orderby('tbl_city.id', 'DESC')->get();


    $citys_count = City::select('tbl_city.id')
        
                ->LeftJoin('tbl_country', 'tbl_country.id', 'tbl_city.country_id')
                ->LeftJoin('tbl_state', 'tbl_state.id', 'tbl_city.state_id')
                ->WHERE('tbl_city.is_deleted', '0');
    if(!empty($keywords)){
      $citys_count = $citys_count->where(function ($query) use ($keywords) {
        $query->where('tbl_city.name', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_country.name', 'like', '%'.$keywords.'%')
              ->orWHERE('tbl_state.name', 'like', '%'.$keywords.'%');
        });
    }
    $citys_count =  $citys->count();


    $data = array(
      "data" => $citys,
      "total" => $citys_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }

  public function edit_city($id){
    $city = City::where('id', $id)
             ->first();
    return response()->json($city); 
  }

  public function update_city($id, Request $request){
    $request->validate([
        'name' => 'required',
        'status' => 'required'
    ]);

    $name_exists = State::WHERE('name', $request->name)->WHERE('country_id', $request->country_id)->WHERE('id', '!=', $id)->first();
    if($name_exists){
      $data = "<span class='text-danger'>Name Already Exists. You can not save State Name!!!</span>";
    } else {
      $city = new City;
      $city = City::find($id);
      $city->status = $request->status;
      $city->name = $request->name;
      $city->country_id = $request->country_id;
      $city->state_id = $request->state_id;
      $city->save();
      $data = "Data Update Successfully!!!";
    }
    return response()->json($data); 
  }


  

  public function city_delete($id){

    $city = City::find($id);
    $city->is_deleted = 1;
    $city->save();

    $data = "Data Deleted";
    return response()->json($data); 
  }


  public function all_city_list(Request $request){


    $state = $request->get("state");

    $citys = City::latest()
                ->WHERE('is_deleted', '0');
    if(!empty($state)){
      $citys = $citys->WHERE('state_id', $state);
    }
    $citys = $citys->get();
    return response()->json($citys); 

  }

  public function all_city_count(Request $request){

    $sql = "SELECT count(a.id) as countID, b.name FROM `tbl_lead` a, tbl_city b WHERE 1 AND a.city = b.id GROUP BY a.city, b.name;";
    $city=DB::select($sql);
    return response()->json($city); 

  }

  public function add_punch_in($id, Request $request){
    $request->validate([
        'remarks' => 'required'
    ]);

    $punch = new Punch;
    $punch->userid = $id;
    $punch->latitude = $request->latitude;
    $punch->longitude = $request->longitude;
    $punch->remarks = $request->remarks;
    $punch->save();
    $result = "Data Store Successfully!!!";
    return response()->json($result);
  }


  public function punch_list(Request $request){

    $page = $request->get('page');
    if($page == 1){
      $offset = 0;
    } else {
      $offset = (($page-1) * 12);
    }

    $punchs = Punch::latest()
                ->skip($offset)
                ->take(12)
                ->get();

    $punchs_count = Punch::all();

    $punchs_count = $punchs_count->count();

    $data = array(
      "data" => $punchs,
      "total" => $punchs_count,
      "per_page" => 12,
      "page" => $page,
      "offset" => $offset
    );
    
    return response()->json($data); 

  }



  
}
