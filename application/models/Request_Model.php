<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Request_Model extends CI_Model {

    public $apiToken = "3a5f469b4e843757e38a565889d019c10150556e";

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function httpGet($url) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Postman-Token: ff2d102d-cca3-4e90-a78c-9e9afed0009a"
            ),
        ));

        $output = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return array(
            'status' => $status,
            'output' => $output
        );
  }

  public function httpPost($url,$params) {
    $postData = '';
    foreach($params as $k => $v) { 
        $postData .= $k . '='.$v.'&';
    }
    $postData = rtrim($postData, '&');

    $ch = curl_init();  

    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false); 
    curl_setopt($ch, CURLOPT_POST, count($postData));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    

    $output = curl_exec($ch);

    curl_close($ch);
    return $output;
}

public function httpPostXML($url,$xml) {

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $xml,
        CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: text/xml",
            "Postman-Token: 3438edde-5338-4b94-85be-7967cf3b06f3"
        ),
    ));

    $output = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    return array(
        'status' => $status,
        'output' => $output
    );
}

public function arrayToXml($array, $rootElement = null, $xml = null) {
    $_xml = $xml;

    if ($_xml === null) {
        $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root/>');
    }

    foreach ($array as $k => $v) {
            if (is_array($v)) { //nested array
                arrayToXml($v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }

        return $_xml->asXML();
    }

    public function imageFromUrl($image_url, $image_path) {
        $fp = fopen ($image_path, 'w+');

        $ch = curl_init($image_url);

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

        $output = curl_exec($ch);

        curl_close($ch);

        fclose($fp);  

        return $output;
    }

    public function apikey()
    {
        return $apiToken;
    }

}

