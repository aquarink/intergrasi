<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TrainAPI extends CI_Controller { 

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
			$depature = "?d=".$this->input->get('depature');
			$arrival = "&a=".$this->input->get('arrival');
			$dateGo = "&date=".$this->input->get('depatureDate');
			$dateReturn = "&ret_date=".$this->input->get('returnDate');
			$adult = "&adult=".$this->input->get('adult');
			$child = "&child=".$this->input->get('child');
			$infant = "&infant=".$this->input->get('invant');
			$class = "&class=".$this->input->get('class');
			$key = "&token=".$this->session->userdata('train_token_session');
			$format = "&output=json";

			// NEW DATA PARAM
			$newRequest = $url.$param.$depature.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$class.$key.$format;
			// $newRequest = $url.$param.$depature.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$class.$key.$format;

			$trainFile = FCPATH.'files/train/search/';
			$trainFileUrl = base_url().'files/train/search/';
			if (!is_dir($trainFile)) {
				mkdir($trainFile, 0777, TRUE);
			}

			$trainSearchFilename = $this->input->get('depature').'_'.$this->input->get('arrival').'_'.$this->input->get('depatureDate').'_'.$this->input->get('returnDate').'_'.$this->input->get('adult').'_'.$this->input->get('child').'_'.$this->input->get('invant').'_'.$this->input->get('class').'.json';

			// FIND ON FILE
			if (file_exists($trainFile.$trainSearchFilename)) {
				$trainJson = $trainFileUrl.$trainSearchFilename;
			} else {
				// IF FILE NOT EXIST
				$getTrainNew = $this->Request_Model->httpGet($newRequest);
				print_r($getTrainNew);
				if($getTrainNew['status'] == 200) {
					if (write_file($trainFile.$trainSearchFilename, $getTrainNew['output'])) {
						$trainJson = $trainFileUrl.$trainSearchFilename;
					} else {
						echo "a";
					}
				} else {
					echo "v";
				}
			}

			$trainData = json_decode($trainJson, true);

			print_r($trainData); exit();

			foreach ($trainData['results']['result'] as $k => $v) {
				// $hotelName[$v['id']]['hotel_id'] = $v['hotel_id'];					
				// $hotelName[$v['id']]['province_name'] = $v['province_name'];							
				// $hotelName[$v['id']]['regional'] = $v['regional'];							
				$findResult[$v['id']]['name'] = $v['value'];						
				// $hotelName[$v['id']]['kecamatan_name'] = $v['kecamatan_name'];							
				// $hotelName[$v['id']]['kelurahan_name'] = $v['kelurahan_name'];							
				// $hotelName[$v['id']]['star_rating'] = $v['star_rating'];							
				// $hotelName[$v['id']]['room_available'] = $v['room_available'];							
				// $hotelName[$v['id']]['room_max_occupancies'] = $v['room_max_occupancies'];
				// $hotelName[$v['id']]['rating'] = $v['rating'];
				// $hotelName[$v['id']]['latitude'] = $v['latitude'];							
				// $hotelName[$v['id']]['longitude'] = $v['longitude'];							
				$findResult[$v['id']]['business_uri'] = base_url().'ViewDetailHotel?detail='.$v['business_uri'];
				// $hotelName[$v['id']]['photo_primary'] = $v['photo_primary'];						
				$findResult[$v['id']]['address'] = strip_tags($v['label_location']);

				$findResult[$v['id']]['mkd'] = $this->session->userdata('train_token_session');							
				// $hotelName[$v['id']]['wifi'] = $v['wifi'];							
				// $hotelName[$v['id']]['promo_name'] = $v['promo_name'];							
				// $hotelName[$v['id']]['price'] = $v['price'];							
				// $hotelName[$v['id']]['total_price'] = $v['total_price'];					
			}

			echo json_encode($findResult);		
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
	public function Get_Train_Seat_Map()
	{

	}

	public function Train_Add_Order()
	{
		
	}
	public function Train_Order()
	{

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

	}

	public function Train_Change_TrainSeat()
	{
		
	}
}
