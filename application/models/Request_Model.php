<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Request_Model extends CI_Model {

    public $apiToken = "3a5f469b4e843757e38a565889d019c10150556e";

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function httpGet($url) {
        $ch = curl_init();  

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        // curl_setopt($ch,CURLOPT_HEADER, false); 

        $output = curl_exec($ch);

        curl_close($ch);
        return $output;
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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        
        $output = curl_exec($ch);
        
        curl_close($ch);
        return $output;
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

