<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FlightAPI extends CI_Controller { 

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

		$this->load->model('Flight_Model');
	}

	public function index()
	{
		echo "Forbhident access : 404";
	}

	public function Search_Flight()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('token_session'));

		if($validate_token) {
			// TRUE
			
			$param = "search/flight";
			$depature = "?d=".$this->input->get('depature');
			$arrival = "&a=".$this->input->get('arrival');
			$dateGo = "&date=".$this->input->get('depatureDate');
			$dateReturn = "&ret_date=".$this->input->get('returnDate');
			$adult = "&adult=".$this->input->get('adult');
			$child = "&child=".$this->input->get('child');
			$infant = "&infant=".$this->input->get('invant');
			$version = "&v=3";
			$key = "&token=".$this->session->userdata('token_session');
			$format = "&output=json";

			// NEW DATA PARAM
			$newRequest = $url.$param.$depature.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$version.$key.$format;

			$flightFile = FCPATH.'files/flight/search/';
			if (!is_dir($flightFile)) {
				mkdir($flightFile, 0777, TRUE);
			}

			$flightDepFilename = $this->session->userdata('token_session').'_'.$this->input->get('depature').'_'.$this->input->get('arrival').'_'.$this->input->get('depatureDate').'_'.$this->input->get('adult').'_'.$this->input->get('child').'_'.$this->input->get('invant').'.json';

			// FIND ON FILE
			if (file_exists($flightFile.$flightDepFilename)) {
				// IF FILE EXIST AND CHECK UPDATE
				$updateRequest = $url."ajax/mCheckFlightUpdated".$depature.$arrival.$dateGo.$adult.$child.$infant.$key.$format;
				$getFlightUpdate = $this->Request_Model->httpGet($updateRequest);
				if($getFlightUpdate['status'] == 200) {

					$updateFlight = json_decode($getFlightUpdate['output'], true);

					// if($updateFlight['update'] > 0) {
					if($updateFlight['update'] == 1) {
						$getFlightNew = $this->Request_Model->httpGet($newRequest);
						if($getFlightNew['status'] == 200) {
							if (write_file($flightFile.$flightDepFilename, $getFlightNew['output'])) {
								$flightsJson = $flightFile.$flightDepFilename;
							}
						}
					} else {
						$flightsJson = $flightFile.$flightDepFilename;
					}
				}
			}  else  {
		   		// IF FILE NOT EXIST
				$getFlightNew = $this->Request_Model->httpGet($newRequest);
				if($getFlightNew['status'] == 200) {
					if (write_file($flightFile.$flightDepFilename, $getFlightNew['output'])) {
						$flightsJson = $flightFile.$flightDepFilename;
					}
				}
			}

			if(!empty($this->input->get('returnDate')) || $this->input->get('returnDate') != '') {
				$flightArrFilename = $this->session->userdata('token_session').'_'.$this->input->get('arrival').'_'.$this->input->get('depature').'_'.$this->input->get('returnDate').'_'.$this->input->get('adult').'_'.$this->input->get('child').'_'.$this->input->get('invant').'.json';

				// WITH RETURN FLIGHT
				// FIND ON FILE
				if (file_exists($flightFile.$flightArrFilename)) {
				// IF FILE EXIST AND CHECK UPDATE
					$updateRequestArr = $url."ajax/mCheckFlightUpdated".$arrival.$depature.$dateGo.$adult.$child.$infant.$key.$format;
					$getFlightUpdateArr = $this->Request_Model->httpGet($updateRequestArr);
					if($getFlightUpdateArr['status'] == 200) {
						$updateFlightArr = json_decode($getFlightUpdateArr['output'], true);

						if($updateFlightArr['update'] > 0) {
							$getFlightNewArr = $this->Request_Model->httpGet($newRequest);
							if($getFlightNewArr['status'] == 200) {
								if (write_file($flightFile.$flightArrFilename, $getFlightNewArr['output'])) {
									$flightsJsonArr = $flightFile.$flightArrFilename;
								}
							}
						} else {
							$flightsJsonArr = $flightFile.$flightArrFilename;
						}
					}
				}  else  {
		   		// IF FILE NOT EXIST
					$getFlightNewArr = $this->Request_Model->httpGet($newRequest);
					if($getFlightNewArr['status'] == 200) {
						if (write_file($flightFile.$flightArrFilename, $getFlightNewArr['output'])) {
							$flightsJsonArr = $flightFile.$flightArrFilename;
						}
					}
				}

				// RETURN OUTPUT

			}


			echo $flightsJson;

			// $request = $url.$param.$depature.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$version.$key.$format;

			// $getResponse = $this->Request_Model->httpGet($request);
			// if($getResponse['status'] == 200) {

			// 	$flights = json_decode($getResponse['output'], true);

			// 	foreach ($flights['departures']['result'] as $key => $value) {
			// 		foreach ($value['flight_infos']['flight_info'] as $k => $v) {
			// 			// FIRST
			// 			$findResult['departures'][$v['flight_number']]['flight_id'] = $value['flight_id'];
			// 			$findResult['departures'][$v['flight_number']]['stop'] = $value['stop'];
			// 			$findResult['departures'][$v['flight_number']]['airlines_name'] = $value['airlines_name'];
			// 			// PRICE
			// 			$findResult['departures'][$v['flight_number']]['price']['price_value'] = $value['price_value'];
			// 			$findResult['departures'][$v['flight_number']]['price']['price_adult'] = $value['price_adult'];
			// 			$findResult['departures'][$v['flight_number']]['price']['price_child'] = $value['price_child'];
			// 			$findResult['departures'][$v['flight_number']]['price']['price_infant'] = $value['price_infant'];

			// 			// SECOND
			// 			// FROM
			// 			$findResult['departures'][$v['flight_number']]['flight_number'] = $v['flight_number'];
			// 			$findResult['departures'][$v['flight_number']]['departure_city'] = $v['departure_city'];
			// 			$findResult['departures'][$v['flight_number']]['departure_city_name'] = $v['departure_city_name'];
			// 			$findResult['departures'][$v['flight_number']]['departure_airport_name'] = $v['departure_airport_name'];
			// 			$findResult['departures'][$v['flight_number']]['departure_airport_terminal'] = $v['terminal'];
			// 			$findResult['departures'][$v['flight_number']]['departure_date_time'] = $v['departure_date_time'];
			// 			// TO
			// 			$findResult['departures'][$v['flight_number']]['arrival_city'] = $v['arrival_city'];
			// 			$findResult['departures'][$v['flight_number']]['departure_city_name'] = $v['arrival_city_name'];
			// 			$findResult['departures'][$v['flight_number']]['arrival_airport_name'] = $v['arrival_airport_name'];
			// 			$findResult['departures'][$v['flight_number']]['arrival_date_time'] = $v['arrival_date_time'];
			// 			// Thumbnail
			// 			$findResult['departures'][$v['flight_number']]['img_src'] = $v['img_src'];
			// 			// DURATION
			// 			$findResult['departures'][$v['flight_number']]['duration_second'] = $v['duration_time'];
			// 			$findResult['departures'][$v['flight_number']]['duration_minute'] = $v['duration_minute'];
			// 			$findResult['departures'][$v['flight_number']]['duration_hour'] = $v['duration_hour'];
			// 			// BAGASI
			// 			$findResult['departures'][$v['flight_number']]['check_in_baggage'] = $v['check_in_baggage'];
			// 			$findResult['departures'][$v['flight_number']]['check_in_baggage_unit'] = $v['check_in_baggage_unit'];
			// 		}
			// 	}

			// 	if($dateReturn != '') {
			// 		foreach ($flights['returns']['result'] as $key => $value) {
			// 			foreach ($value['flight_infos']['flight_info'] as $k => $v) {
			// 				// FIRST
			// 				$findResult['returns'][$v['flight_number']]['flight_id'] = $value['flight_id'];
			// 				$findResult['returns'][$v['flight_number']]['stop'] = $value['stop'];
			// 				$findResult['returns'][$v['flight_number']]['airlines_name'] = $value['airlines_name'];
			// 				// PRICE
			// 				$findResult['returns'][$v['flight_number']]['price']['price_value'] = $value['price_value'];
			// 				$findResult['returns'][$v['flight_number']]['price']['price_adult'] = $value['price_adult'];
			// 				$findResult['returns'][$v['flight_number']]['price']['price_child'] = $value['price_child'];
			// 				$findResult['returns'][$v['flight_number']]['price']['price_infant'] = $value['price_infant'];

			// 				// SECOND
			// 				// FROM
			// 				$findResult['returns'][$v['flight_number']]['flight_number'] = $v['flight_number'];
			// 				$findResult['returns'][$v['flight_number']]['departure_city'] = $v['departure_city'];
			// 				$findResult['returns'][$v['flight_number']]['departure_city_name'] = $v['departure_city_name'];
			// 				$findResult['returns'][$v['flight_number']]['departure_airport_name'] = $v['departure_airport_name'];
			// 				$findResult['returns'][$v['flight_number']]['departure_airport_terminal'] = $v['terminal'];
			// 				$findResult['returns'][$v['flight_number']]['departure_date_time'] = $v['departure_date_time'];
			// 				// TO
			// 				$findResult['returns'][$v['flight_number']]['arrival_city'] = $v['arrival_city'];
			// 				$findResult['returns'][$v['flight_number']]['departure_city_name'] = $v['arrival_city_name'];
			// 				$findResult['returns'][$v['flight_number']]['arrival_airport_name'] = $v['arrival_airport_name'];
			// 				$findResult['returns'][$v['flight_number']]['arrival_date_time'] = $v['arrival_date_time'];
			// 				// Thumbnail
			// 				$findResult['returns'][$v['flight_number']]['img_src'] = $v['img_src'];
			// 				// DURATION
			// 				$findResult['returns'][$v['flight_number']]['duration_second'] = $v['duration_time'];
			// 				$findResult['returns'][$v['flight_number']]['duration_minute'] = $v['duration_minute'];
			// 				$findResult['returns'][$v['flight_number']]['duration_hour'] = $v['duration_hour'];
			// 				// BAGASI
			// 				$findResult['returns'][$v['flight_number']]['check_in_baggage'] = $v['check_in_baggage'];
			// 				$findResult['returns'][$v['flight_number']]['check_in_baggage_unit'] = $v['check_in_baggage_unit'];
			// 			}
			// 		}
			// 	}


			// 	echo json_encode($findResult);
			// }
		}
	}

	public function Get_Nearest_Airport()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('token_session'));

		// IP
		$ipaddress = '61.94.127.202';
		// if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		// 	$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		// }
		// else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		// 	$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		// }
		// else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
		// 	$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		// }
		// else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
		// 	$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		// }
		// else if(isset($_SERVER['HTTP_FORWARDED'])) {
		// 	$ipaddress = $_SERVER['HTTP_FORWARDED'];
		// }
		// else if(isset($_SERVER['REMOTE_ADDR'])) {
		// 	$ipaddress = $_SERVER['REMOTE_ADDR'];
		// }
		// else {
		// 	$ipaddress = 'UNKNOWN';
		// }

		if($validate_token) {
			// TRUE
			$param = "flight_api/getNearestAirport";
			$key = "?token=".$this->session->userdata('token_session');
			$ip = "&ip=$ipaddress";
			$format = "&output=json";

			$request = $url.$param.$key.$ip.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {

				$nearestAirport  = json_decode($getResponse['output'], true);

				foreach ($nearestAirport['search'] as $key => $value) {
					// USER INFORM
					$user[] = $value;
				}

				$nearAirport[$user[0]]['latitude'] = $user[1];
				$nearAirport[$user[0]]['longitude'] = $user[2];

				foreach ($nearestAirport['nearest_airports']['airport'] as $k => $v) {
						// FIRST
					$nearAirport[$user[0]]['airport_code'] = $v['airport_code'];
					$nearAirport[$user[0]]['airport_city'] = $v['location_name'];
					$nearAirport[$user[0]]['airport_name'] = $v['business_name'];	
					$nearAirport[$user[0]]['distance'] = $v['distance'];		
				}

				echo json_encode($nearAirport);
			}
		}
	}
	public function Get_Popular_Airport_Destination()
	{

	}

	public function Search_Airport()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('token_session'));

		if($validate_token) {
			// TRUE
			$search = $this->input->get('search');
			$find = $this->Flight_Model->searchAirportLike($search);

			echo json_encode($find);
		}
	}
	public function Insert_Airport()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('token_session'));

		if($validate_token) {
			$param = "flight_api/all_airport";
			$key = "?token=".$this->session->userdata('token_session');
			$format = "&output=json";

			$request = $url.$param.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$allAirports = json_decode($getResponse['output'], true);		

				foreach ($allAirports['all_airport']['airport'] as $key => $value) {

					$checkAirports = $this->Flight_Model->searchAirportAPI($value['airport_code'],$value['airport_name'],$value['country_id'],$value['country_name']);

					if(count($checkAirports) == 0) {
						$this->Flight_Model->insertNewAirport($value['airport_code'],$value['airport_name'],$value['country_id'],$value['country_name'],date('Y-m-d H:i:s'));
					}			
				}

				echo "OK";
			}
		}
	}
	public function Check_Update()
	{
		echo "Access Permission need more";
	}

	public function Get_Lion_Captcha()
	{
		echo "Access Permission need more";
	}
	public function Get_Flight_Data()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('token_session'));

		if($validate_token) {
			// TRUE
			$param = "flight_api/get_flight_data";
			$flightId = "?flight_id=16825942";
			$key = "&token=".$this->session->userdata('token_session');
			$dateGo = "&date=2018-05-15";
			$version = "&v=3";			
			$format = "&output=json";

			$request = $url.$param.$flightId.$key.$dateGo.$version.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$checkFlight = json_decode($getResponse['output'], true);

				echo "<pre>";
				print_r($checkFlight);

				echo "Dummy Flight ID not found";
			}
		}
	}

	public function Flight_Add_Order()
	{
		echo "Access Permission need more";
	}
	public function Flight_Order()
	{
		echo "Access Permission need more";
	}

	public function Delete_Order()
	{
		echo "Access Permission need more";
	}
	public function Flight_Checkout_Page_Request()
	{
		echo "Access Permission need more";
	}
	public function Flight_Checkout_Login()
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
