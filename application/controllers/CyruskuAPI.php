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
		$url = $this->config->item('cyrusku_api_url');
		$msisdn = '081510193960';
		$productid = 'zz';
		$userid = $this->config->item('cyrusku_api_username');
		$password = $this->config->item('cyrusku_api_password');
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
		$send = $this->Request_Model->httpPostXML($url,$valueXml);
		print_r($send);
		// Response XML
		// $responseXml = new SimpleXMLElement($valueXml);
		
		// <result>0</result>
		// <msg> BERHASIL: Isi pulsa berhasil. No trx: 10001 (Rp 19750). Saldo: Rp xxx. No HP: 0812345678.</msg>
		// <trxid>10001</trxid>
		// <partner_trxid>12345</partner_trxid> 

		// INSERT
	}
	public function S_N()
	{
		// trxid=CyruskuTrxId&partner_trxid=TRXID&msg=pesan_cyrusku_berisi_sn
		$rs_trxid = $this->input->get('trxid');
		$trxid = $this->input->get('partner_trxid');
		$msg = $this->input->get('msg');
		$msgExpl = explode("S/N",$msg);
		$sn = $msgExpl[1];

		// UPDATE SN

	}
	public function Reversal()
	{
		// ?result=11&msg=Reversal&trxid=10001&partner_trxid=12345&msisdn=62812 345678 
		$rs_trxid = $this->input->get('trxid');
		$trxid = $this->input->get('partner_trxid');
		$msisdn = $this->input->get('msisdn');

		// KIRIM ULANG

	}

	public function Cek_Tagihan()
	{
		$url = $this->config->item('cyrusku_api_url');
		$msisdn = '081510193960';
		$productid = 'PLN';
		$userid = $this->config->item('cyrusku_api_username');
		$password = $this->config->item('cyrusku_api_password');
		$time = date('His');
		$lastMsisdn = substr($msisdn, -4);
		$reverse = strrev(substr($msisdn, -4));
		$result = $time.$lastMsisdn XOR $reverse.$password;
		$trxid = date('YmdHis');
		$signature = base64_encode($result);

		$valueXml = '<?xml version="1.0" ?><evoucher>';
		$valueXml .= '<command>CEK</command>';
		$valueXml .= '<product>'.$productid.'</product>';
		$valueXml .= '<userid>'.$userid.'</userid>';
		$valueXml .= '<time>'.$time.'</time>';
		$valueXml .= '<msisdn>'.$msisdn.'</msisdn>';
		$valueXml .= '<partner_trxid>'.$productid.'</partner_trxid>';
		$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

		// Request
		$send = $this->Request_Model->httpPostXML($url,$valueXml);
		print_r($send);
		// Response XML
		// $responseXml = new SimpleXMLElement($valueXml);
		
		// <result>0</result>
		// <msg> BERHASIL: Isi pulsa berhasil. No trx: 10001 (Rp 19750). Saldo: Rp xxx. No HP: 0812345678.</msg>
		// <trxid>10001</trxid>
		// <partner_trxid>12345</partner_trxid>
		// <amount>92072</amount> jumlah tagihan yang harus dibayar
		// <ori_amount>90072</ori_amount> jumlah tagihan tanpa biaya admin
		// <admin_fee>2000</admin_fee> biaya admin 
	}
	public function Bayar_Tagihan()
	{
		$url = $this->config->item('cyrusku_api_url');
		$msisdn = '081510193960';
		$productid = 'PLN';
		$amount = '20000';
		$userid = $this->config->item('cyrusku_api_username');
		$password = $this->config->item('cyrusku_api_password');
		$time = date('His');
		$lastMsisdn = substr($msisdn, -4);
		$reverse = strrev(substr($msisdn, -4));
		$result = $time.$lastMsisdn XOR $reverse.$password;
		$trxid = date('YmdHis');
		$signature = base64_encode($result);

		$valueXml = '<?xml version="1.0" ?><evoucher>';
		$valueXml .= '<command>PAY</command>';
		$valueXml .= '<product>'.$productid.'</product>';
		$valueXml .= '<userid>'.$userid.'</userid>';
		$valueXml .= '<time>'.$time.'</time>';
		$valueXml .= '<msisdn>'.$msisdn.'</msisdn>';
		$valueXml .= '<partner_trxid>'.$productid.'</partner_trxid>';
		$valueXml .= '<amount>'.$amount.'</amount>';
		$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

		// Request
		$send = $this->Request_Model->httpPostXML($url,$valueXml);
		print_r($send);
		// Response XML
		// $responseXml = new SimpleXMLElement($valueXml);
		
		// <result>0</result>
		// <msg> BERHASIL: Isi pulsa berhasil. No trx: 10001 (Rp 19750). Saldo: Rp xxx. No HP: 0812345678.</msg>
		// <trxid>10001</trxid>
		// <partner_trxid>12345</partner_trxid>
		// <amount>92072</amount> jumlah tagihan yang dibayar 
	}
	public function Cek_Saldo()
	{
		$url = $this->config->item('cyrusku_api_url');
		$amount = '20000';
		$userid = $this->config->item('cyrusku_api_username');
		$password = $this->config->item('cyrusku_api_password');
		$time = date('His');
		$lastMsisdn = substr($msisdn, -4);
		$reverse = strrev(substr($msisdn, -4));
		$result = $time.$lastMsisdn XOR $reverse.$password;
		$signature = base64_encode($result);

		$valueXml = '<?xml version="1.0" ?><evoucher>';
		$valueXml .= '<command>SALDO</command>';
		$valueXml .= '<userid>'.$userid.'</userid>';
		$valueXml .= '<time>'.$time.'</time>';
		$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

		// Request
		$send = $this->Request_Model->httpPostXML($url,$valueXml);
		print_r($send);
		// Response XML
		// $responseXml = new SimpleXMLElement($valueXml);
		
		// <result>0</result>
		// <msg>Saldo Rp. 251680</msg>
		// <saldo>10001</trxid>
	}
}
