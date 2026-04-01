<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\CallerDesk;
use App\Models\Tasktodo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Exception;
class TeamController extends Controller
{
   public function add_team(Request $request)
   {
    //   print_r($request->teammember_id);
    //     die;
    //   $request->validate
    //   ([
    //         'team_name' => 'required',
    //         'buisness_id' => 'required',
    //         'manager_id' => 'required',
    //         'teamlead_id' => 'required',
    //         'teammember_id' => 'required',
    //     ]);
        
          define('Tm','Team');
          $team = new Team;
          $team->team_id = Tm.substr(uniqid(), -3);
          $team->team_name = $request->team_name;
          $team->buisness_id = $request->buisness_id;
          $team->manager_id  = $request->manager_id;
          $team->teamlead_id  = $request->teamlead_id;
       if(is_array($request->teammember_id))
       {
                  $team->teammember_id = implode(", ",$request->teammember_id);
       }
       else
       {
                  $team->teammember_id = $request->teammember_id;
       }
      //$team->teammember_id = $request->teammember_id;
      $team->created_at  = date('m/d/Y h:i:s a', time());
      $team->status ='1';
      $team->save();
      $result = "Data Store Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
        
      return response()->json($data);
   }
   
   public function edit_team($id)
   {
      
      $team = Team::where('team_id', $id)->with('bussiness','manager','team_lead','members')->first();
      $data = array(
      "status" => 200,
      "response" =>$team, 
      );
      return response()->json($data);
   }
   
    public function all_team(Request $request,$id='null')
   {
            $page = $request->get('page');
            if($page == 1){
              $offset = 0;
            } 
            else 
            {
              $offset = (($page-1) * 12);
            }
             if($id=='null')
            {
                      $query = "
                        SELECT 
                            t.team_id,
                            t.team_name,
                            (SELECT b1.buisness_name FROM tbl_buisness AS b1 WHERE b1.buisness_id = t.buisness_id LIMIT 1) AS buisness_name,
                            (SELECT b2.buisness_logo FROM tbl_buisness AS b2 WHERE b2.buisness_id = t.buisness_id LIMIT 1) AS buisness_logo,
                            (SELECT b3.buisness_email FROM tbl_buisness AS b3 WHERE b3.buisness_id = t.buisness_id LIMIT 1) AS buisness_email,
                            (SELECT b4.buisness_contact FROM tbl_buisness AS b4 WHERE b4.buisness_id = t.buisness_id LIMIT 1) AS buisness_contact,
                            (SELECT b5.buisness_address FROM tbl_buisness AS b5 WHERE b5.buisness_id = t.buisness_id LIMIT 1) AS buisness_address,
                            (SELECT b6.buisness_url FROM tbl_buisness AS b6 WHERE b6.buisness_id = t.buisness_id LIMIT 1) AS buisness_url,
                            (SELECT b7.buisness_description FROM tbl_buisness AS b7 WHERE b7.buisness_id = t.buisness_id LIMIT 1) AS buisness_description,
                            m.first_name AS manager_first_name,
                            m.last_name AS manager_last_name,
                            m.photo AS manager_photo,
                            m.dateofbirth AS manager_date_of_birth,
                            m.gender AS manager_gender,
                            m.contactno AS manager_contact_number,
                            m.email AS manager_email,
                            tl.first_name AS team_lead_first_name,
                            tl.last_name AS team_lead_last_name,
                            tl.photo AS team_lead_photo,
                            tl.dateofbirth AS team_lead_date_of_birth,
                            tl.gender AS team_lead_gender,
                            tl.contactno AS team_lead_contact_number,
                            tl.email AS team_lead_email,
                            (SELECT GROUP_CONCAT(CONCAT(tm.first_name, ' ', tm.last_name)) FROM tbl_employees AS tm WHERE FIND_IN_SET(tm.employee_id, t.teammember_id)) AS team_members
                        FROM 
                            tbl_team AS t
                        LEFT JOIN 
                            tbl_employees AS m ON t.manager_id = m.employee_id
                        LEFT JOIN 
                            tbl_employees AS tl ON t.teamlead_id = tl.employee_id
                    ";
                    $results = DB::select($query);
                    $buisness_count = count($results);
                    $data1 = array(
                          "status" => 200,
                          "data" => $results,
                          "total" =>$buisness_count,
                          "per_page" => 12,
                          "page" => $page,
                          "offset" => $offset
                       );
            }
            else
            {
               $results1 = DB::select("
                        SELECT 
                            t.team_name,
                            (
                                SELECT b1.buisness_name 
                                FROM tbl_buisness AS b1 
                                WHERE b1.buisness_id = t.buisness_id
                                LIMIT 1
                            ) AS buisness_name,
                            (
                                SELECT b2.buisness_logo 
                                FROM tbl_buisness AS b2 
                                WHERE b2.buisness_id = t.buisness_id
                                LIMIT 1
                            ) AS buisness_logo,
                            (
                                SELECT b3.buisness_email 
                                FROM tbl_buisness AS b3 
                                WHERE b3.buisness_id = t.buisness_id
                                LIMIT 1
                            ) AS buisness_email,
                            (
                                SELECT b4.buisness_contact 
                                FROM tbl_buisness AS b4 
                                WHERE b4.buisness_id = t.buisness_id
                                LIMIT 1
                            ) AS buisness_contact,
                            (
                                SELECT b5.buisness_address 
                                FROM tbl_buisness AS b5 
                                WHERE b5.buisness_id = t.buisness_id
                                LIMIT 1
                            ) AS buisness_address,
                            (
                                SELECT b6.buisness_url 
                                FROM tbl_buisness AS b6 
                                WHERE b6.buisness_id = t.buisness_id
                                LIMIT 1
                            ) AS buisness_url,
                            (
                                SELECT b7.buisness_description 
                                FROM tbl_buisness AS b7 
                                WHERE b7.buisness_id = t.buisness_id
                                LIMIT 1
                            ) AS buisness_description,
                            m.first_name AS manager_first_name,
                            m.last_name AS manager_last_name,
                            m.photo AS manager_photo,
                            m.dateofbirth AS manager_date_of_birth,
                            m.gender AS manager_gender,
                            m.contactno AS manager_contact_number,
                            m.email AS manager_email,
                            tl.first_name AS team_lead_first_name,
                            tl.last_name AS team_lead_last_name,
                            tl.photo AS team_lead_photo,
                            tl.dateofbirth AS team_lead_date_of_birth,
                            tl.gender AS team_lead_gender,
                            tl.contactno AS team_lead_contact_number,
                            tl.email AS team_lead_email,
                            (
                                SELECT GROUP_CONCAT(CONCAT(tm.first_name, ' ', tm.last_name)) 
                                FROM tbl_employees AS tm 
                                WHERE FIND_IN_SET(tm.employee_id, t.teammember_id)
                            ) AS team_members
                        FROM 
                            tbl_team AS t
                        LEFT JOIN 
                            tbl_employees AS m ON t.manager_id = m.employee_id
                        LEFT JOIN 
                            tbl_employees AS tl ON t.teamlead_id = tl.employee_id
                        WHERE
                            t.team_id = :id
                    ", ['id' => $id]);
                    $count = count($results1);
                    $data1 = array(
                          "status" => 200,
                          "data" => $results1,
                          "total" => $count,
                          "per_page" => 12,
                          "page" => $page,
                          "offset" => $offset
                       );
                
            }
              
              return response()->json($data1);
    }
    
     public function update_team(Request $request,$id)
    {
                if(is_array($request->teammember_id))
               {
                          $data = implode(", ",$request->teammember_id);
               }
               else
               {
                          $data = $request->teammember_id;
               }
       
              $update = Team::where('team_id', $id)->update([
                    'team_id' => $request->team_id,
                    'team_name' => $request->team_name,
                    'buisness_id' => $request->buisness_id,
                    'manager_id' =>$request->manager_id,
                    'teamlead_id' =>$request->teamlead_id,
                    'teammember_id' =>$data,
                    'status'=>'1',
                    'updated_at' => now(),
                    // add more fields as needed
                ]);
                
                $result = "Task updated Successfully!!!";
                $data = array(
                  "status" => 200,
                  "response" => $result, 
                  );
                return response()->json($data);

   }
   
   public function delete_team($id)
   {
      $update = Team::where('team_id',$id)->update(['status'=>'0']);
      $result = "Data Deleted Successfully!!!";
      $data = array(
      "status" => 200,
      "response" =>$result, 
      );
      
      return response()->json($data);
   }
  
   
  
  
}
