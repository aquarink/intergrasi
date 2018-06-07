<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TrainAPI extends CI_Controller { 

	function __construct() 
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->helper('form');
		$this->load->helper('file');

		$this->load->library('session');
		$this->load->library('user_agent');
		$this->load->library('form_validation');

		$this->load->model('Request_Model');
		$this->load->model('Token_Model');

		$this->load->model('Train_Model');
	}

	public function index()
	{ 
		echo "Forbhident access : 404";
	}

	public function Search_Train()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('train_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('train_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('train_token_session'));

		if($validate_token) {
			// TRUE
			$param = "search/train";
			$departure = "?d=".$this->input->get('departure');
			$arrival = "&a=".$this->input->get('arrival');
			$dateGo = "&date=".$this->input->get('departureDate');
			$dateReturn = "&ret_date=".$this->input->get('returnDate');
			$adult = "&adult=".$this->input->get('adult');
			$child = "&child=".$this->input->get('child');
			$infant = "&infant=".$this->input->get('invant');
			$class = "&class=".$this->input->get('class');
			$key = "&token=".$this->session->userdata('train_token_session');
			$format = "&output=json";

			// NEW DATA PARAM
			$newRequest = $url.$param.$departure.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$class.$key.$format;
			// $newRequest = $url.$param.$departure.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$class.$key.$format;

			$trainFile = FCPATH.'files/train/search/';
			$trainFileUrl = base_url().'files/train/search/';
			if (!is_dir($trainFile)) {
				mkdir($trainFile, 0777, TRUE);
			}

			$trainSearchFilename = $this->input->get('departure').'_'.$this->input->get('arrival').'_'.$this->input->get('departureDate').'_'.$this->input->get('returnDate').'_'.$this->input->get('adult').'_'.$this->input->get('child').'_'.$this->input->get('invant').'_'.$this->input->get('class').'.json';

			// FIND ON FILE
			if (file_exists($trainFile.$trainSearchFilename)) {
				$trainJson = $trainFileUrl.$trainSearchFilename;
			} else {
				// IF FILE NOT EXIST
				$getTrainNew = $this->Request_Model->httpGet($newRequest);
				if($getTrainNew['status'] == 200) {
					if (write_file($trainFile.$trainSearchFilename, $getTrainNew['output'])) {
						$trainJson = $trainFileUrl.$trainSearchFilename;
					} else {
						$res = array(
							'error' => 1,
							'datas' => 0,
							'msg' => 'Write file failed 86'				
						);
					}
				} else {
					$res = array(
						'error' => 1,
						'datas' => 0,
						'msg' => 'Disconnect from API 100'				
					);
				}
			}

			if(!isset($res)) {
				$getTrainData = $this->Request_Model->httpGet($trainJson);
				if($getTrainData['status'] == 200) {

					$trainData = json_decode($getTrainData['output'], true);
					foreach ($trainData['departures']['result'] as $k => $v) {							
						$findResult['departures'][$v['id']]['id'] = $v['id'];						
						$findResult['departures'][$v['id']]['detailAvailability'] = $v['detail_availability'];
						$findResult['departures'][$v['id']]['trainId'] = $v['train_id'];
						$findResult['departures'][$v['id']]['trainName'] = $v['train_name'];
						$findResult['departures'][$v['id']]['departureStationCode'] = $v['departure_station'];
						$findResult['departures'][$v['id']]['departureStationName'] = $v['departure_station'];
						$findResult['departures'][$v['id']]['departureStationCity'] = $v['departure_city_name'];
						$findResult['departures'][$v['id']]['departureTime'] = $v['departure_time'];
						$findResult['departures'][$v['id']]['arrivalStationCode'] = $v['arrival_station'];
						$findResult['departures'][$v['id']]['arrivalStationName'] = $v['arrival_station_name'];
						$findResult['departures'][$v['id']]['arrivalStationCity'] = $v['arrival_city_name'];
						$findResult['departures'][$v['id']]['arrivalTime'] = $v['arrival_time'];
						$findResult['departures'][$v['id']]['class'] = $v['class_name_lang'];
						$findResult['departures'][$v['id']]['subclass'] = $v['subclass_name'];
						$findResult['departures'][$v['id']]['isPromo'] = $v['is_promo'];
						$findResult['departures'][$v['id']]['diffHour'] = $v['time_diff']['diff_hour'];
						$findResult['departures'][$v['id']]['diffMinute'] = $v['time_diff']['diff_minute'];

						// PRICE
						$findResult['departures'][$v['id']]['priceAdult'] = $v['price_adult_clean'];
						$findResult['departures'][$v['id']]['priceChild'] = $v['price_child_clean'];
						$findResult['departures'][$v['id']]['priceInfant'] = $v['price_infant_clean'];
						$findResult['departures'][$v['id']]['priceTotal'] = $v['price_total_clean'];
					}

					if(!empty($this->input->get('returnDate')) || $this->input->get('returnDate') != '') {
						foreach ($trainData['returns']['result'] as $k => $v) {							
							$findResult['returns'][$v['id']]['id'] = $v['id'];						
							$findResult['returns'][$v['id']]['detailAvailability'] = $v['detail_availability'];
							$findResult['returns'][$v['id']]['trainId'] = $v['train_id'];
							$findResult['returns'][$v['id']]['trainName'] = $v['train_name'];
							$findResult['returns'][$v['id']]['departureStationCode'] = $v['departure_station'];
							$findResult['returns'][$v['id']]['departureStationName'] = $v['departure_station'];
							$findResult['returns'][$v['id']]['departureStationCity'] = $v['departure_city_name'];
							$findResult['returns'][$v['id']]['departureTime'] = $v['departure_time'];
							$findResult['returns'][$v['id']]['arrivalStationCode'] = $v['arrival_station'];
							$findResult['returns'][$v['id']]['arrivalStationName'] = $v['arrival_station_name'];
							$findResult['returns'][$v['id']]['arrivalStationCity'] = $v['arrival_city_name'];
							$findResult['returns'][$v['id']]['arrivalTime'] = $v['arrival_time'];
							$findResult['returns'][$v['id']]['class'] = $v['class_name_lang'];
							$findResult['returns'][$v['id']]['subclass'] = $v['subclass_name'];
							$findResult['returns'][$v['id']]['isPromo'] = $v['is_promo'];
							$findResult['returns'][$v['id']]['diffHour'] = $v['time_diff']['diff_hour'];
							$findResult['returns'][$v['id']]['diffMinute'] = $v['time_diff']['diff_minute'];

							// PRICE
							$findResult['returns'][$v['id']]['priceAdult'] = $v['price_adult_clean'];
							$findResult['returns'][$v['id']]['priceChild'] = $v['price_child_clean'];
							$findResult['returns'][$v['id']]['priceInfant'] = $v['price_infant_clean'];
							$findResult['returns'][$v['id']]['priceTotal'] = $v['price_total_clean'];
						}
					}

					$res = array(
						'error' => 0,
						'datas' => $findResult,
						'msg' => 'Get train data success'				
					);

					echo json_encode($res);	
				} else {
					$res = array(
						'error' => 1,
						'datas' => 0,
						'msg' => 'Disconnect from API 140'				
					);

					echo json_encode($res);
				}
			} else {
				$ress = array(
					'error' => 1,
					'datas' => 0,
					'msg' => $res['msg']				
				);

				echo json_encode($ress);
			}
		}
	}
	public function Insert_Station()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('train_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('train_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('train_token_session'));

		if($validate_token) {
			$param = "train_api/train_station";
			$key = "?token=".$this->config->item('tiket_api_key');
			
			$format = "&output=json";

			$request = $url.$param.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);

			$allStations = json_decode($getResponse, true);

			foreach ($allStations['stations']['station'] as $key => $value) {

				$checkStation = $this->Train_Model->searchStationAPI($value['station_code'],$value['station_name'],$value['city_name']);

				if(count($checkStation) == 0) {
					$this->Train_Model->insertNewStation($value['station_code'],$value['station_name'],$value['city_name'],$value['type'],$value['longitude'],$value['latitude'],date('Y-m-d H:i:s'));
				}			
			}

			echo "OK";
		}
	}
	public function Search_Station()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('train_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('train_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('train_token_session'));

		if($validate_token) {
			// TRUE
			$search = $this->input->get('search');
			$find = $this->Train_Model->searchStationLike($search);

			echo json_encode($find);
		}
	}
	public function Train_Add_Order()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('train_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('train_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('train_token_session'));

		if($validate_token) { 
			// TRUE
			$param = "order/add/train";
			$departure = "?d=".$this->input->get('departureCode');
			$arrival = "&a=".$this->input->get('arrivalCode');
			$dateGo = "&date=".$this->input->get('departureDate');
			$dateReturn = "&ret_date=".$this->input->get('returnDate');
			$adult = "&adult=".$this->input->get('adult');
			$child = "&child=".$this->input->get('child');
			$infant = "&infant=".$this->input->get('invant');

			$additional = "&conSalutation=Mr&conFirstName=b&conLastName=bl&conPhone=0878121&conEmailAddress=be@scom&nameAdult1=a&IdCardAdult1=111&noHpAdult1=%2B62878222&salutationAdult1=Mr&birthDateAdult1=1990-02-02&nameChild1&salutationChild1&birthDateChild1&salutationInfant1&nameInfant1&birthDateInfant1";

			$trainId = "&train_id=".$this->input->get('depTrainId');
			$subclass = "&subclass=".$this->input->get('depSubclass');	
			$retTrainId = "&train_id_ret=".$this->input->get('retTrainId');
			$retSubclass = "&subclass_ret=".$this->input->get('retSubclass');
			$key = "&token=".$this->session->userdata('train_token_session');
			$format = "&output=json";

			$request = $url.$param.$departure.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$additional.$trainId.$subclass.$retTrainId.$retSubclass.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$getDetail = json_decode($getResponse['output'], true);
					
				if($getDetail['diagnostic']['status'] > 200) {
					// SUCCESS
					// Hotel_Order API
					$paramOrder = "order";		
					$keyOrder = "&token=".$this->session->userdata('train_token_session');
					$formatOrder = "&output=json";

					$requestOrder = $url.$paramOrder.$keyOrder.$formatOrder;
					// $requestOrder = "https://api-sandbox.tiket.com/order?token=624cb009761ecadbd0042685a4a9d491f475b7df&output=json";

					$getResponseOrder = $this->Request_Model->httpGet($requestOrder);
					if($getResponseOrder['status'] == 200) {
						$getOrder = json_decode($getResponseOrder['output'], true);

						// LIST
						$orderData[$getOrder['myorder']['order_id']]['orderId'] = $getOrder['myorder']['order_id'];

						// ORDER DATA
						foreach ($getOrder['myorder']['data'] as $kData => $vData) {
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['expire'] = $vData['expire'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderDetailId'] = $vData['order_detail_id'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderExpireDatetime'] = $vData['order_expire_datetime'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderType'] = $vData['order_type'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['customerPrice'] = $vData['customer_price'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['customerCurrency'] = $vData['customer_currency'];

							// DETAIL
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['arrivalDatetime'] = $vData['detail']['arrival_datetime'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['departureDatetime'] = $vData['detail']['departure_datetime'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['countAdult'] = $vData['detail']['count_adult'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['countChild'] = $vData['detail']['count_child'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['bookCode'] = $vData['detail']['book_code'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['trainSubclass'] = $vData['detail']['train_subclass'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['tiketSeating'] = $vData['detail']['tiket_seating'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['numCode'] = $vData['detail']['num_code'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['trainFrom'] = $vData['detail']['train_from'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['trainTo'] = $vData['detail']['train_to'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['trainName'] = $vData['detail']['train_name'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['subclassType'] = $vData['detail']['subclass_type'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['className'] = $vData['detail']['class_name'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['fromStation'] = $vData['detail']['train_from_station'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['toStation'] = $vData['detail']['train_to_station'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['price'] = $vData['detail']['price'];

							// TAX
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderPhoto'] = $vData['order_photo'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['taxCharge'] = $vData['tax_and_charge'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['subtotalCharge'] = $vData['subtotal_and_charge'];

							// ORDERS MANAGE
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['deleteOrder'] = $vData['delete_uri'];
						}

						// TOTAL
						$orderData[$getOrder['myorder']['order_id']]['total'] = $getOrder['myorder']['total'];
						$orderData[$getOrder['myorder']['order_id']]['totalTax'] = $getOrder['myorder']['total_tax'];
						$orderData[$getOrder['myorder']['order_id']]['totalWithoutTax'] = $getOrder['myorder']['total_without_tax'];
						$orderData[$getOrder['myorder']['order_id']]['countInstallment'] = $getOrder['myorder']['count_installment'];

						// CHECKOUT
						$orderData[$getOrder['myorder']['order_id']]['checkoutUrl'] = $getOrder['checkout'];

						$data = array(
							'error' => $getDetail['diagnostic']['status'],
							'msg' => 'Success get data',
							'datas' => $orderData
						);

						echo json_encode($data);
					} else {
						$data = array(
							'error' => 500,
							'msg' => 'Error 500',
							'datas' => 0
						);

						echo json_encode($data);
					}
				} elseif($getDetail['diagnostic']['status'] == 211) {
					$data = array(
						'error' => $getDetail['diagnostic']['status'],
						'msg' => $getDetail['diagnostic']['error_msgs'],
						'datas' => 0
					);

					echo json_encode($data);
				}
				
			} else {
				$data = array(
					'error' => 500,
					'msg' => 'Error 500',
					'datas' => 0
				);

				echo json_encode($data);
			}
		}
	}

	public function Get_Train_Seat_Map()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('train_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('train_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('train_token_session'));

		if($validate_token) { 
			// TRUE
			$param = "general_api/get_train_seat_map";
			$date = "?date=".$this->input->get('date');
			$trainId = "&train_id=".$this->input->get('trainId');
			$subclass = "&subclass=".$this->input->get('subclass');
			$from = "&org=".$this->input->get('departureCode');
			$to = "&dest=".$this->input->get('arrivalCode');
			$key = "&token=".$this->session->userdata('train_token_session');
			$format = "&output=json";

			$request = $url.$param.$date.$trainId.$subclass.$from.$to.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$getSeat = json_decode($getResponse['output'], true);

				$data = array(
					'error' => 0,
					'msg' => 'Get seat data success',
					'datas' => $getSeat['result'];
				);

				echo json_encode($data);
			} else {
				$data = array(
					'error' => 500,
					'msg' => 'Error 500',
					'datas' => 0
				);

				echo json_encode($data);
			}
		}
	}
	public function Train_Change_TrainSeat()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('train_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('train_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('train_token_session'));

		if($validate_token) { 
			// TRUE
			$param = "general_api/train_change_seat";
			$bookingCode = "?booking_code=".$this->input->get('bookingCode');
			$kodeWagon = "&kode_wagon=".$this->input->get('kodeWagon');
			$nomorKursi = "&nomor_kursi=".$this->input->get('nomorKursi');
			$orderDetailId = "&order_detail_id=".$this->input->get('orderDetailId');
			$key = "&token=".$this->session->userdata('train_token_session');
			$format = "&output=json";

			$request = $url.$param.$bookingCode.$kodeWagon.$nomorKursi.$orderDetailId.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$getSeat = json_decode($getResponse['output'], true);

				$data = array(
					'error' => 0,
					'msg' => 'Get seat data success',
					'datas' => $getSeat['result'];
				);

				echo json_encode($data);
			} else {
				$data = array(
					'error' => 500,
					'msg' => 'Error 500',
					'datas' => 0
				);

				echo json_encode($data);
			}
		}
	}
	public function Train_Checkout_Page_Request()
	{
		
	}
	public function Train_Checkout_Login()
	{

	}
	public function Train_Available_Payment()
	{
		
	}
	public function Train_Checkout_Payment()
	{

	}
	public function Train_Register()
	{
		
	}
	public function Train_Search_Promo()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('train_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('train_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('train_token_session'));

		if($validate_token) { 
			// TRUE
			$param = "train_api/train_promo";
			$departure = "?d=".$this->input->get('departureCode');
			$dateGo = "&date=".$this->input->get('departureDate');
			$key = "&token=".$this->session->userdata('train_token_session');
			$format = "&output=json";

			https://api-sandbox.tiket.com/train_api/train_promo?d=GMR&date=2012-06-28&token=4206f440696c91b855581fb2eafac225&output=json

			$request = $url.$param.$departure.$dateGo.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$getPromo = json_decode($getResponse['output'], true);

				$data = array(
					'error' => 0,
					'msg' => 'Get seat data success',
					'datas' => $getPromo;
				);

				echo json_encode($data);
			} else {
				$data = array(
					'error' => 500,
					'msg' => 'Error 500',
					'datas' => 0
				);

				echo json_encode($data);
			}
		}
	}
}
