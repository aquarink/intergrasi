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

	}
	public function Insert_Station()
	{
		$token_tiket = $this->config->item('tiket_api_key');
		$validate_token = $this->Token_Model->validateToken($token_tiket);

		if($validate_token) {
			$url = $this->config->item('tiket_api_url_dev');
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
		$token_tiket = $this->config->item('tiket_api_key');
		$validate_token = $this->Token_Model->validateToken($token_tiket);

		if($validate_token) {
			// TRUE
			$search = 'gede';
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
