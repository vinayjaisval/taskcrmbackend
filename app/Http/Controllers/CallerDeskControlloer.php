<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CallerDeskControlloer extends Controller
{
    public function index()
    {

    }

    // Astrologer
    public function caller_desk_webhook(Request $request){
        

        $SourceNumber = substr($request->SourceNumber,-10);
        $DialWhomNumber = substr($request->DialWhomNumber,-10);
        
        DB::table('tbl_callerdesk')
        ->insert(
            [
                'sourcenumber' => $SourceNumber,
                'destinationnumber' => $request->DestinationNumber,
                'dialwhomnumber' => $DialWhomNumber,
                'callduration' => $request->CallDuration,
                'coins' => $request->coins,
                'status' => $request->Status,
                'starttime' => $request->StartTime,
                'endtime' => $request->EndTime,
                'callsid' => $request->CallSid,
                'callrecordingurl' => $request->CallRecordingUrl,
                'direction' => $request->Direction,
                'campid' => $request->campid,
                'talkduration' => $request->TalkDuration
            ]
        );

        // Deduct Balance From Wallet 
        // Astro Details-
        $astrologer =  DB::table('tbl_users')
            ->WHERE('phone', $DialWhomNumber)
            ->first();
        $astro_id = $astrologer->id;
        $astro_name = $astrologer->name;
        $astro_amount = $astrologer->amount;

        $astro_profile = DB::table('tbl_users_profile')
           ->WHERE('userid', $astro_id)
           ->first();
        $per_min_charge = $astro_profile->per_min_call;
        $total_call = $astro_profile->total_call;
        $commission = ($astro_profile->commission/100);
        $per_sec_charge = ($per_min_charge / 60);

        // Customer Details-
        $customer =  DB::table('tbl_users')
                ->WHERE('phone', $SourceNumber)
                ->first();
        $user_amount = $customer->amount;
        $user_id = $customer->id;

        $total_charge = ($request->TalkDuration * $per_sec_charge);
        $total_astro_comm = ($total_charge * $commission);
        $total_astro_comm_fnl = ($total_charge - $total_astro_comm);
        $total_astro_transfer = ($total_astro_comm_fnl + $astro_amount);

        $total_cst_deduct = ($user_amount - $total_charge);

        // Astro Update
        $astrologer = new Login;
        $astrologer = Login::find($astro_id);
        $astrologer->amount = $total_astro_transfer;
        $astrologer->save();

        // Customer Update
        $cstr = new Login;
        $cstr = Login::find($user_id);
        $cstr->amount = $total_cst_deduct;
        $cstr->save();

        $total_call_cal = ($request->TalkDuration + $total_call);

        // Total Call Time-
        DB::table('tbl_users_profile')
        ->WHERE('userid', $astro_id)
        ->update([
          'total_call' => $total_call_cal
      ]);

        // History Table 
        DB::table('tbl_trasection_history')
        ->insert(
            [
                'astro_id' => $astro_id,
                'cust_id' => $user_id,
                'amount' => $total_charge,
                'astro_comm' => $total_astro_transfer,
                'user_deduct' => $total_charge,
                'duration' => $request->TalkDuration,
                'type' => 'Click to Call',
                'date_time' => date('Y-m-d H:i:s')
            ]
        );

        DB::table('tbl_trasactions')
        ->insert(
            [
                'userid' => $astro_id,
                'credit' => $total_astro_transfer,
                'debit' => '0',
                'type' => 'Click to Call',
                'remarks' => '',
                'added_by' => $user_id,
                'created_at' => date('Y-m-d H:i:s')
            ]
        );

        DB::table('tbl_trasactions')
        ->insert(
            [
                'userid' => $user_id,
                'credit' => '0',
                'debit' => $total_charge,
                'type' => 'Click to Call',
                'remarks' => 'Call Iniate to Astrologer - '.$astro_name,
                'added_by' => $user_id,
                'created_at' => date('Y-m-d H:i:s')
            ]
        );

        
        $data = "Records save successfully !!!!";
     
        return response()->json($data);
        
    }

    public function click_to_call($id, $AstroEdtId, Request $request){

        $id = $id;
        $AstroEdtId = $AstroEdtId;

        $astrologer =  DB::table('tbl_users')
             ->WHERE('id', $AstroEdtId)
             ->first();
        $astro_phone = $astrologer->phone;

        $customer =  DB::table('tbl_users')
             ->WHERE('id', $id)
             ->first();
        $cust_phone = $customer->phone;

        $api = "https://app.callerdesk.io/api/click_to_call_v2?calling_party_a=".$astro_phone."&calling_party_b=".$cust_phone."&deskphone=1206853054&authcode=2c463718d70809dad964904823ae0f29&call_from_did=1";
        $response = Http::get($api);
    }

}