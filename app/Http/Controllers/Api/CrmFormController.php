<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CrmFormController extends Controller
{
    public function createUser(Request $request)
    {
        //dd($request);
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomInt = '';
        $length = 4;

        for($i = 0; $i < $length; $i++) {
            $randomInt .= $characters[random_int(0, $charactersLength - 1)];
        }
        
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|unique:users'
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'phone_verified_code' => $randomInt
        ]);

        $data = [
            "header" => [
                'login' => 'sms0069ts',
                'pwd' => '1234qaz',
                'CgPN' => 1490
            ],
            "body" => [
                "message_id_in" => $randomInt,
                "CdPN" => $request->phone,
                "text" => "Your code " . $randomInt
            ]
        ];

        $url = 'http://sms.etc.uz:8084/single-sms';

        $response = Http::post($url, $data);

        if ($response->successful()) {
            $responseData = $response->json();
            return redirect()->route('send.get');
        } else {
            $statusCode = $response->status();
            return response()->json($statusCode, 404);
        }

    }   

    public function sendGet()
    {
        return view('smsPost');
    }

    public function sendPost(Request $request)
    {   
       
        $tenMinute = Carbon::now()->addMinute(10);

        $user_code = User::query()->where('phone_verified_code', '=', $request->notify)->first();
        
        if($user_code == false){
            return response()->json("Your code not enabled!", 200);
        }

        $user_code->update([
            'phone_verified_at' => 'true'
        ]);
        
        if($tenMinute !== $request->created_at){
            if($user_code->phone_verified_at == "true"){
                $queryUrl = 'https://office.astrum.uz/rest/33/eko4d7ti6fax0tx1/crm.lead.add.json';
                $queryData = http_build_query(array(
                'fields' => array(
                    "TITLE" => $user_code->first_name .' '. $user_code->last_name,
                    "NAME" => $user_code->first_name,
                    "LAST_NAME" => $user_code->last_name,
                    "STATUS_ID" => "NEW",
                    "OPENED" => "Y",
                    "SOURCE_ID" => "UC_TIAVP5",
                    "ASSIGNED_BY_ID" => 1,
                    "PHONE" => array(array("VALUE" => $user_code->phone, "VALUE_TYPE" => "WORK" )),
                    "EMAIL" => array(array("VALUE" => $user_code->email, "VALUE_TYPE" => "WORK" )),
                ),
                'params' => array("REGISTER_SONET_EVENT" => "Y")
            ));
                
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_POST => 1,
                    CURLOPT_HEADER => 0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $queryUrl,
                    CURLOPT_POSTFIELDS => $queryData,
                ));

                $result = curl_exec($curl);
                curl_close($curl);
                
                try {
                    return response()->json($result);
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }else{
                return response()->json("Your code is not correct.", 200);
            }
        }

        return response()->json("Your code is outdated.", 200);
        
    }
}