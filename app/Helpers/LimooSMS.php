<?php

namespace App\Helpers;

use App\Constants\Constants;

class LimooSMS
{
    private string $url = 'https://api.limosms.com/api';

    public function sendOtpCode(string $phoneNumber = '')
    {
        if (!$phoneNumber) {
            return Constants::INVALID_PHONE_NUMBER_ERROR;
        }
        $url =$this->url.'/sendcode';
        $post_data = json_encode(array(
            'Mobile' => $phoneNumber,
        ));
        $apiKey = env('LIMOO_SMS_API_KEY');
        $process = curl_init();
        curl_setopt( $process,CURLOPT_URL,$url);
        curl_setopt( $process, CURLOPT_TIMEOUT,30);
        curl_setopt( $process, CURLOPT_POST, 1);
        curl_setopt( $process, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt( $process, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt( $process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt( $process, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt( $process, CURLOPT_HTTPHEADER, array('Content-Type: application/json'
        ,"ApiKey:$apiKey"));
        $return = curl_exec( $process);
        $httpcode = curl_getinfo( $process, CURLINFO_HTTP_CODE);
        curl_close($process);
        $decoded = json_decode($return);
        var_dump($decoded);
        return $decoded;
    }
}
