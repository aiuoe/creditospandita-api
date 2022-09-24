<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Loandisk_apiController extends Controller
{
    public $url; 
    public $auth_code;
    public $public_key;
    public $branch_id;   
    
    public $data, $headers;
    
    private $send_headers;
    
    public function __construct(){
        $this->url = env("URI_LOANDISK",null);
        $this->auth_code = env("AUTH_CODE_LOANDISK",null);
        $this->public_key = env("PUBLIC_KEY_LOANDISK",null);
        $this->branch_id = env("BRANCH_ID_LOANDISK",null);

    }

    public function call ($endpoint, $type, $data = null, $from = null, $count= null, $branch_check = true){
        
      $this->send_headers = array('Content-Type: application/json',
          'Authorization: Basic ' . $this->auth_code
        );

        $curl_options = array(
          CURLOPT_HEADER => 1,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_FRESH_CONNECT => TRUE,
          CURLOPT_CONNECTTIMEOUT => 0,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_HTTPHEADER => $this->send_headers,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_FOLLOWLOCATION => true
        );
        
        #other related to type and data
        switch($type) {
          case 'GET':
            $data_url = 
            (!empty($data) ? '/' . $data : '') .
            (!empty($from) ? '/from/' . $from : '').
            (!empty($count) ? '/count/' . $count : '');
            break;

          case 'POST':
            $data_url = '';
            $data = json_encode($data);
            $curl_options[CURLOPT_POST] = true;
            $curl_options[CURLOPT_POSTFIELDS] = $data;
            break;
            
          case 'PUT':
            $data_url = '';
            $data = json_encode($data);
            $curl_options[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $curl_options[CURLOPT_POSTFIELDS] = $data;
            break;
          case 'DELETE':
            $data_url = (!empty($data) ? '/' . $data : '');
            $curl_options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            break;
        }

        #url to connect to
        $_url = $this->url . "/" . $this->public_key . "/" . ($branch_check ? $this->branch_id : 'ma') . "/" . $endpoint. $data_url;

        #initialize the connection
        $ch = curl_init();


        $curl_options[CURLOPT_URL] = $_url;
        curl_setopt_array($ch, $curl_options);
        
        // Send the request
        $result = curl_exec($ch);
        $error = curl_errno($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);

        //
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);
        
        curl_close($ch);
        
        $this->headers = $header;
        $this->data = $body;
        
        #Mix headers & body
        return true;
    }
    
    public function get(Request $request) {
        // $endpoint, $data, $from = null, $count = null
        // $arreglo=json_decode($request->getContent(), true);
      return $this->call($request->endpoint, 'GET', $request->data, $arreglo['from'], $arreglo['$count']);
    }

    public function getLoan(Request $request) {
      // $endpoint, $data, $from = null, $count = null
      $arreglo=json_decode($request->getContent(), true);
    $response=$this->call('loan/borrower', 'GET', $arreglo['borrower_id'], $arreglo['page'], $arreglo['n_page']);
    $datica=json_decode($this->data);
    $paso1=get_object_vars($datica);
    $paso2=get_object_vars($paso1['response']);
    // var_dump($paso2['Results'][0]);
    $arregloFinal=array();
    foreach($paso2['Results'][0] as $crediticio){
      
      array_push($arregloFinal, get_object_vars($crediticio));

    }
// var_dump($arregloFinal);
    return response()->json([
        
      'response'          => $response,
      'data'          => $arregloFinal,
      'headers'          => $this->headers,
      
  ]);
  }


  
    
    public function post(Request $request) {
        // $endpoint, $data = null
    
        $arreglo=json_decode($request->getContent(), true);
        $response=$this->call($arreglo['endpoint'], 'POST', $arreglo['data']);
        return response()->json([
        
          'response'          => $response,
          'data'          => $this->data,
          'headers'          => $this->headers,
          
      ]);
    }
    
    public function put($endpoint, $data = null) {
      return $this->call($endpoint, 'PUT', $data);
    }
    
    public function delete($endpoint, $data = null) {
      return $this->call($endpoint, 'DELETE', $data);
    }
    public function authenticate_login($staff_email_encoded, $staff_password_encoded) {
        return $this->call('staff/authenticate/email/'.$staff_email_encoded.'/password/'.$staff_password_encoded, 'GET', null, null, null, false);
    }
    public function authenticate_token($token) {
        return $this->call('staff/authenticate/staff_token', 'GET', $token, null, null, false);
    }
}
