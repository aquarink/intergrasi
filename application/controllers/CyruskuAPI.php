<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CyruskuAPI extends CI_Controller { 

	function __construct() 
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('form');

		$this->load->library('session');
		$this->load->library('user_agent');
		$this->load->library('form_validation');

		$this->load->model('Request_Model');
		$this->load->model('Token_Model');
	}

	public function index()
	{
		echo "Forbhident access : 404";
	}

	public function Top_Up()
	{	
		$url = '';
		$msisdn = '081510193960';
		$productid = 'zz';
		$userid = 'xx';
		$password = 'abcdef';
		$time = date('His');
		$lastMsisdn = substr($msisdn, -4);
		$reverse = strrev(substr($msisdn, -4));
		$result = $time.$lastMsisdn XOR $reverse.$password;
		$trxid = date('YmdHis');
		$signature = base64_encode($result);

		$valueXml = '<?xml version="1.0" ?><evoucher>';
		$valueXml .= '<command>TOPUP</command>';
		$valueXml .= '<product>'.$productid.'</product>';
		$valueXml .= '<userid>'.$userid.'</userid>';
		$valueXml .= '<time>'.$time.'</time>';
		$valueXml .= '<msisdn>'.$msisdn.'</msisdn>';
		$valueXml .= '<partner_trxid>'.$productid.'</partner_trxid>';
		$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

		// Request
		// $send = $this->Request_Model->httpPostXML($url,$valueXml);
		// Response XML
		$responseXml = new SimpleXMLElement($valueXml);
		echo "<pre>";
		print_r($responseXml);
	}
	public function Reversal()
	{
		echo "Access Permission need more";
	}

	public function Cek_Tagihan()
	{
		echo "Access Permission need more";
	}
	public function Bayar_Tagihan()
	{
		echo "Access Permission need more";
	}
	public function Cek_Saldo()
	{
		echo "Access Permission need more";
	}
	public function Flight_Checkout_Costumer()
	{
		echo "Access Permission need more";
	}

	public function Flight_Available_Payment()
	{
		echo "Access Permission need more";
	}
	public function Flight_Checkout_Payment()
	{
		echo "Access Permission need more";
	}
}
