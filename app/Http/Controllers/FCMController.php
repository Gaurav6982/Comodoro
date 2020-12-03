<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FCMController extends Controller
{
    public function index(){
        return view('fcm');
    }
    public function send(){
        $token = "ezNWxfegYns:APA91bE5Soajq6N-pJ0KU64nNN8IMgi8XoAE_jKs49IkEbSnBIZYbgfZpA_txuTNAigSGPyQXgxfnC8qrMHQo47V2y2ZZNkwA1uU9cTHwLcqR7xyuhDLGaURCbxPo95VoDQGT1mpJAne";  
        $from ="AAAANSYA4L8:APA91bGtYw6IKZ5ow7MErN7F6nm6tQF6Z7nb1VbZx9y2C_DXpljqGmPP51o-zWbpAGW_KPY_4PZEZ57vXMfC4SpqZ7qBXZHbsDOv_aCwiOFbDqyalX1Oi1vHmoDzIgwlLHSx_tS2y-AZ";
        $msg = array
              (
                'body'  => "Testing BOdy",
                'title' => "Hi, THi is test",
                'receiver' => 'erw',
                'icon'  => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
                'sound' => 'mySound'/*Default sound*/
              );

        $fields = array
                (
                    'to'        => $token,
                    'notification'  => $msg
                );

        $headers = array
                (
                    'Authorization: key=' . $from,
                    'Content-Type: application/json'
                );
        //#Send Reponse To FireBase Server 
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        // dd($result);
        curl_close( $ch );
        return back();
    }
}
