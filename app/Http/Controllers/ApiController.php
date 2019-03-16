<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    private $api_key = '62QRKZMCOG0D1AWA0AZ3KOX2AAB64SBU';
    
    private $url = 'https://cbis-rest-api.citybreak.com/v1/api/';

    private $headers;

    private $options;

    public function __construct() {

        $this->headers = array(
            'ApiKey:' . $this->api_key,
            'Accept:application/json',
            'Content-Type:application/json',
            'Accept-language:sv-SE'
        );

        $this->options = array(
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_SSL_VERIFYPEER => false
        );

    }

    public function call() {

        $ch = curl_init();
        curl_setopt_array($ch, $this->options);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return "Error: " . curl_error($ch);
        }

        curl_close($ch);

        return $response;

    }

    public function get($endpoint, Request $request) {
        
        $data = $request->all();

        $this->options[CURLOPT_URL] .= $endpoint;
        $this->options[CURLOPT_POSTFIELDS] = json_encode($data);
        $this->options[CURLOPT_POST] = false;
        $this->options[CURLOPT_HTTPGET] = true;

        return $this->call();

    }

    public function post($endpoint, Request $request) {

        $data = $request->all();

        $this->options[CURLOPT_URL] .= $endpoint;

        $this->options[CURLOPT_POST] = count($data);

        $this->options[CURLOPT_POSTFIELDS] = json_encode($data);

        return $this->call();

    }

}
