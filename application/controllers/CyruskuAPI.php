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

		$this->load->model('Cyrusku_Model');
	}

	public function index()
	{
		echo "Forbhident access : 404";
	}

	public function Cyrusku_Top_Up()
	{	
		if($this->input->get('msisdn') != '' || !empty($this->input->get('msisdn'))) {
			$url = $this->config->item('cyrusku_api_url');
			$msisdn = $this->input->get('msisdn');
			$productid = $this->input->get('product');
			$amount = '';
			$userid = $this->config->item('cyrusku_api_username');
			$password = $this->config->item('cyrusku_api_password');
			$time = date('His');
			$lastMsisdn = substr($msisdn, -4);
			$reverse = strrev(substr($msisdn, -4));
			$result = $time.$lastMsisdn XOR $reverse.$password;
			$trxid = date('YmdHis');
			$signature = base64_encode($result);

			$valueXml = '<evoucher>';
			$valueXml .= '<command>TOPUP</command>';
			$valueXml .= '<product>'.$productid.'</product>';
			$valueXml .= '<userid>'.$userid.'</userid>';
			$valueXml .= '<time>'.$time.'</time>';
			$valueXml .= '<msisdn>'.$msisdn.'</msisdn>';
			$valueXml .= '<partner_trxid>'.$trxid.'</partner_trxid>';
			$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

			// Request
			$send = $this->Request_Model->httpPostXML($url,$valueXml);
			if($send['status'] == 200) {
				// Response XML
				$responseXml = simplexml_load_string($send['output'], "SimpleXMLElement", LIBXML_NOCDATA);
				$XmlToJson = json_encode($responseXml);
				$JsonToArray = json_decode($XmlToJson,TRUE);

				// <result>0</result>
				// <msg> BERHASIL: Isi pulsa berhasil. No trx: 10001 (Rp 19750). Saldo: Rp xxx. No HP: 0812345678.</msg>
				// <trxid>10001</trxid>
				// <partner_trxid>12345</partner_trxid> 
				$rs_result = $JsonToArray['result'];
				$rs_msg = $JsonToArray['msg'];
				$rs_trxid = $JsonToArray['trxid'];
				$dr_sn = '';
				$send_type = 'ori';

				// INSERT
				$trxSave = $this->Cyrusku_Model->insertTrx('TOPUP',$productid,$amount,$msisdn,$trxid,$signature,$rs_result,$rs_msg,$rs_trxid,$dr_sn,$send_type);

				if($trxSave > 0) {
					// RESPONSE
					$res = array(
						'error' => 0,
						'status' => 'PROCESS',
						'data' => '',
						'msg' => 'Topup '.$productid.' ke '.$msisdn.' diproses'
					);

					echo json_encode($res);
				}
			} else {
				// RESPONSE
				$res = array(
					'error' => 1,
					'status' => 'TOPUP FAILED',
					'data' => '',
					'msg' => 'Topup '.$productid.' ke '.$msisdn.' disconnect from API'
				);

				echo json_encode($res);
			}
		} else {
			// RESPONSE
			$res = array(
				'error' => 1,
				'status' => 'TOPUP FAILED',
				'data' => '',
				'msg' => 'Topup param kosong'
			);

			echo json_encode($res);
		}
	}
	public function Cyrusku_Sn()
	{
		if($this->input->get('trxid') != '' || !empty($this->input->get('trxid'))) {
			// trxid=CyruskuTrxId&partner_trxid=TRXID&msg=pesan_cyrusku_berisi_sn
			$rs_trxid = $this->input->get('trxid');
			$trxid = $this->input->get('partner_trxid');
			$msg = $this->input->get('msg');
			$msgExpl = explode("S/N",$msg);
			$dr_sn = $msgExpl[1];

			// UPDATE SN
			$updSn = $this->Cyrusku_Model->updateSn($rs_trxid,$trxid,$dr_sn);

			if($updSn > 0) {
				// RESPONSE
				$res = array(
					'error' => 0,
					'status' => 'SN PROCESS',
					'data' => '',
					'msg' => 'Update S/N '.$rs_trxid.' diproses'
				);

				echo json_encode($res);
			} else {
				// RESPONSE
				$res = array(
					'error' => 1,
					'status' => 'SN FAILED',
					'data' => '',
					'msg' => 'Update S/N '.$rs_trxid.' tidak ditemukan'
				);

				echo json_encode($res);
			}
		} else {
			// RESPONSE
			$res = array(
				'error' => 1,
				'status' => 'SN FAILED',
				'data' => '',
				'msg' => 'Update S/N hanya untuk webhook'
			);

			echo json_encode($res);
		}

	}
	public function Cyrusku_Reversal()
	{
		// ?result=11&msg=Reversal&trxid=10001&partner_trxid=12345&msisdn=62812 345678 
		$rs_trxid = $this->input->get('trxid');
		$trxid = $this->input->get('partner_trxid');
		$msisdn = $this->input->get('msisdn');

		// KIRIM ULANG
		$reversal = $this->Cyrusku_Model->searchReversal($rs_trxid,$trxid,$msisdn);
		if(count($reversal) > 0) {
			foreach ($reversal as $key => $value) {
				$url = $this->config->item('cyrusku_api_url');
				$msisdn = $value->msisdn;
				$productid = $value->product;
				$amount = $this->input->get('amount');
				$userid = $this->config->item('cyrusku_api_username');
				$password = $this->config->item('cyrusku_api_password');
				$time = date('His');
				$lastMsisdn = substr($msisdn, -4);
				$reverse = strrev(substr($msisdn, -4));
				$result = $time.$lastMsisdn XOR $reverse.$password;
				$trxid = date('YmdHis');
				$signature = base64_encode($result);

				$valueXml = '<evoucher>';
				$valueXml .= '<command>TOPUP</command>';
				$valueXml .= '<product>'.$productid.'</product>';
				$valueXml .= '<userid>'.$userid.'</userid>';
				$valueXml .= '<time>'.$time.'</time>';
				$valueXml .= '<msisdn>'.$msisdn.'</msisdn>';
				$valueXml .= '<partner_trxid>'.$trxid.'</partner_trxid>';
				$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

				// Request
				$send = $this->Request_Model->httpPostXML($url,$valueXml);
				if($send['status'] == 200) {
					// Response XML
					$responseXml = simplexml_load_string($send['output'], "SimpleXMLElement", LIBXML_NOCDATA);
					$XmlToJson = json_encode($responseXml);
					$JsonToArray = json_decode($XmlToJson,TRUE);

					// <result>0</result>
					// <msg> BERHASIL: Isi pulsa berhasil. No trx: 10001 (Rp 19750). Saldo: Rp xxx. No HP: 0812345678.</msg>
					// <trxid>10001</trxid>
					// <partner_trxid>12345</partner_trxid> 
					$rs_result = $JsonToArray['result'];
					$rs_msg = $JsonToArray['msg'];
					$rs_trxid = $JsonToArray['trxid'];
					$dr_sn = '';
					$send_type = 'rev';

					// INSERT
					$trxSave = $this->Cyrusku_Model->insertTrx('TOPUP',$productid,$amount,$msisdn,$trxid,$signature,$rs_result,$rs_msg,$rs_trxid,$dr_sn,$send_type);

					if($trxSave > 0) {
						// RESPONSE
						$res = array(
							'error' => 0,
							'status' => 'REVERSAL PROCESS',
							'data' => '',
							'msg' => 'Reversal topup '.$productid.' ke '.$msisdn.' diproses'
						);

						echo json_encode($res);
					}
				} else {
					// RESPONSE
					$res = array(
						'error' => 1,
						'status' => 'REVERSAL FAILED',
						'data' => '',
						'msg' => 'Reversal topup '.$productid.' ke '.$msisdn.' disconnect from API'
					);

					echo json_encode($res);
				}
			}
		} else {
			// RESPONSE
			$res = array(
				'error' => 1,
				'status' => 'REVERSAL FAILED',
				'data' => '',
				'msg' => 'Reversal data not found'
			);

			echo json_encode($res);
		}

	}

	public function Cyrusku_Cek_Tagihan()
	{
		if($this->input->get('msisdn') != '' || !empty($this->input->get('msisdn'))) {
			$url = $this->config->item('cyrusku_api_url');
			$msisdn = $this->input->get('msisdn');
			$productid = 'PLN';
			$amount = '';
			$userid = $this->config->item('cyrusku_api_username');
			$password = $this->config->item('cyrusku_api_password');
			$time = date('His');
			$lastMsisdn = substr($msisdn, -4);
			$reverse = strrev(substr($msisdn, -4));
			$result = $time.$lastMsisdn XOR $reverse.$password;
			$trxid = date('YmdHis');
			$signature = base64_encode($result);

			$valueXml = '<evoucher>';
			$valueXml .= '<command>CEK</command>';
			$valueXml .= '<product>'.$productid.'</product>';
			$valueXml .= '<userid>'.$userid.'</userid>';
			$valueXml .= '<time>'.$time.'</time>';
			$valueXml .= '<msisdn>'.$msisdn.'</msisdn>';
			$valueXml .= '<partner_trxid>'.$trxid.'</partner_trxid>';
			$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

			// Request
			$send = $this->Request_Model->httpPostXML($url,$valueXml);
			if($send['status'] == 200) {
				// Response XML
				$responseXml = simplexml_load_string($send['output'], "SimpleXMLElement", LIBXML_NOCDATA);
				$XmlToJson = json_encode($responseXml);
				$JsonToArray = json_decode($XmlToJson,TRUE);

				// <result>0</result>
				// <msg> BERHASIL: Isi pulsa berhasil. No trx: 10001 (Rp 19750). Saldo: Rp xxx. No HP: 0812345678.</msg>
				// <trxid>10001</trxid>
				// <partner_trxid>12345</partner_trxid>
				// <amount>92072</amount> jumlah tagihan yang harus dibayar
				// <ori_amount>90072</ori_amount> jumlah tagihan tanpa biaya admin
				// <admin_fee>2000</admin_fee> biaya admin 
				$rs_result = $JsonToArray['result'];
				$rs_msg = $JsonToArray['msg'];
				$rs_trxid = $JsonToArray['trxid'];
				$dr_sn = '';
				$send_type = 'ori';

				$amount = $JsonToArray['amount'];

				// INSERT
				$trxSave = $this->Cyrusku_Model->insertTrx('CEK',$productid,$amount,$msisdn,$trxid,$signature,$rs_result,$rs_msg,$rs_trxid,$dr_sn,$send_type);

				if($trxSave > 0) {
					// RESPONSE
					$res = array(
						'error' => 0,
						'status' => 'CEK TAGIHAN PROCESS',
						'data' => array(
							'to' => $msisdn,
							'amount' => $JsonToArray['amount'],
							'admin' => $JsonToArray['admin_fee']
						),
						'msg' => 'Cek tagihan '.$productid.' ke '.$msisdn.' diproses'
					);

					echo json_encode($res);
				}
			} else {
				// RESPONSE
				$res = array(
					'error' => 1,
					'status' => 'BILL CHECK FAILED',
					'data' => '',
					'msg' => 'Cek tagihan '.$productid.' ke '.$msisdn.' disconnect from API'
				);

				echo json_encode($res);
			}
		} else {
			// RESPONSE
			$res = array(
				'error' => 1,
				'status' => 'BILL CHECK FAILED',
				'data' => '',
				'msg' => 'Cek tagihan param kosong'
			);

			echo json_encode($res);
		}
	}
	public function Cyrusku_Bayar_Tagihan()
	{
		if($this->input->get('msisdn') != '' || !empty($this->input->get('msisdn'))) {
			$url = $this->config->item('cyrusku_api_url');
			$msisdn = $this->input->get('msisdn');
			$productid = 'PLN';
			$amount = $this->input->get('amount');
			$userid = $this->config->item('cyrusku_api_username');
			$password = $this->config->item('cyrusku_api_password');
			$time = date('His');
			$lastMsisdn = substr($msisdn, -4);
			$reverse = strrev(substr($msisdn, -4));
			$result = $time.$lastMsisdn XOR $reverse.$password;
			$trxid = date('YmdHis');
			$signature = base64_encode($result);

			$valueXml = '<evoucher>';
			$valueXml .= '<command>PAY</command>';
			$valueXml .= '<product>'.$productid.'</product>';
			$valueXml .= '<userid>'.$userid.'</userid>';
			$valueXml .= '<time>'.$time.'</time>';
			$valueXml .= '<msisdn>'.$msisdn.'</msisdn>';
			$valueXml .= '<partner_trxid>'.$trxid.'</partner_trxid>';
			$valueXml .= '<amount>'.$amount.'</amount>';
			$valueXml .= '<signature>'.$signature.'</signature></evoucher>';

			// Request
			$send = $this->Request_Model->httpPostXML($url,$valueXml);
			if($send['status'] == 200) {
				// Response XML
				$responseXml = simplexml_load_string($send['output'], "SimpleXMLElement", LIBXML_NOCDATA);
				$XmlToJson = json_encode($responseXml);
				$JsonToArray = json_decode($XmlToJson,TRUE);

				// <result>0</result>
				// <msg> BERHASIL: Isi pulsa berhasil. No trx: 10001 (Rp 19750). Saldo: Rp xxx. No HP: 0812345678.</msg>
				// <trxid>10001</trxid>
				// <partner_trxid>12345</partner_trxid>
				// <amount>92072</amount> jumlah tagihan yang dibayar 
				$rs_result = $JsonToArray['result'];
				$rs_msg = $JsonToArray['msg'];
				$rs_trxid = $JsonToArray['trxid'];
				$dr_sn = '';
				$send_type = 'ori';

				$amount = $JsonToArray['amount'];
				// INSERT
				$trxSave = $this->Cyrusku_Model->insertTrx('PAY',$productid,$amount,$msisdn,$trxid,$signature,$rs_result,$rs_msg,$rs_trxid,$dr_sn,$send_type);

				if($trxSave > 0) {
					// RESPONSE
					$res = array(
						'error' => 0,
						'status' => 'BAYAR TAGIHAN PROCESS',
						'data' => array(
							'to' => $msisdn,
							'amount' => $JsonToArray['amount'],
							'admin' => $JsonToArray['admin_fee']
						),
						'msg' => 'Bayar tagihan '.$productid.' ke '.$msisdn.' diproses'
					);

					echo json_encode($res);
				}
			} else {
				// RESPONSE
				$res = array(
					'error' => 1,
					'status' => 'BILL PAY FAILED',
					'data' => '',
					'msg' => 'Bayar tagihan '.$productid.' ke '.$msisdn.' disconnect from API'
				);

				echo json_encode($res);
			}
		} else {
			// RESPONSE
			$res = array(
				'error' => 1,
				'status' => 'BILL PAY FAILED',
				'data' => '',
				'msg' => 'Bayar tagihan param kosong'
			);

			echo json_encode($res);
		}
	}
	public function Cyrusku_Cek_Saldo()
	{
		$url = $this->config->item('cyrusku_api_url');
		$userid = $this->config->item('cyrusku_api_username');
		$password = $this->config->item('cyrusku_api_password');
		$time = date('His');
		$lastUserid = substr($userid, -4);
		$reverse = strrev(substr($userid, -4));
		$a = $time.$lastUserid;
		$b = $reverse.$password;
		$signature = base64_encode($a ^ $b);

		$valueXml = '<evoucher><command>SALDO</command><userid>'.$userid.'</userid><time>'.$time.'</time><signature>'.$signature.'</signature></evoucher>';

		// Request
		$send = $this->Request_Model->httpPostXML($url,$valueXml);
		if($send['status'] == 200) {
			// Response XML
			$responseXml = simplexml_load_string($send['output'], "SimpleXMLElement", LIBXML_NOCDATA);
			$XmlToJson = json_encode($responseXml);
			$JsonToArray = json_decode($XmlToJson,TRUE);

			// RESPONSE
			$res = array(
				'error' => 0,
				'status' => 'SALDO CHECK SUCCESS',
				'data' => array(
					'saldo' =>  $JsonToArray['saldo']
				),
				'msg' => 'Cek saldo sukses'
			);
			echo json_encode($res);
		} else {
			// RESPONSE
			$res = array(
				'error' => 1,
				'status' => 'SALDO CHECK FAILED',
				'data' => '',
				'msg' => 'Cek saldo '.$productid.' ke '.$msisdn.' disconnect from API'
			);

			echo json_encode($res);
		}
	}
	public function Cyrusku_Cek_Product()
	{
		$url = "https://cyrusku.cyruspad.com/interkoneksi/productcyrusku.asp";
		$userid = $this->config->item('cyrusku_api_username');
		$password = $this->config->item('cyrusku_api_password');
		$time = date('YmdHis');
		$signature = md5($userid.'PRODUCTCYRUSKU'.$time.$password);

		$product_id = '';
		$operator = '';

		$params = 'command=PRODUCTCYRUSKU';
		$params .= '&username=6282298407898';
		$params .= '&time=20180531062722';
		$params .= '&signature=0ab6955b586702402d6259b5c1f9bc9d';
		$params .= '&product_id=';
		$params .= '&operator=';

		// Request
		$send = $this->Request_Model->httpPost($url,$params);
		if($send['status'] == 200) {
			$outputToArray = json_decode($send['output'],true);
			// RESPONSE
			$res = array(
				'error' => 0,
				'status' => 'PRODUCT CHECK SUCCESS',
				'data' => $outputToArray,
				'msg' => 'Cek produk dan harga sukses'
			);
			echo json_encode($res);
		} else {
			// RESPONSE
			$res = array(
				'error' => 1,
				'status' => 'REVERSAL FAILED',
				'data' => '',
				'msg' => 'Cek tagihan '.$productid.' ke '.$msisdn.' disconnect from API'
			);

			echo json_encode($res);
		}
	}
}
