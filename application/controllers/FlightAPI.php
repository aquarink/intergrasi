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
		if(empty($this->session->userdata('flight_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('flight_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('flight_token_session'));

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
			$key = "&token=".$this->session->userdata('flight_token_session');
			$format = "&output=json";

			// NEW DATA PARAM
			$newRequest = $url.$param.$depature.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$version.$key.$format;

			$flightFile = FCPATH.'files/flight/search/';
			$flightFileUrl = base_url().'files/flight/search/';
			if (!is_dir($flightFile)) {
				mkdir($flightFile, 0777, TRUE);
			}

			$flightFilename = $this->input->get('depature').'_'.$this->input->get('arrival').'_'.$this->input->get('depatureDate').'_'.$this->input->get('returnDate').'_'.$this->input->get('adult').'_'.$this->input->get('child').'_'.$this->input->get('invant').'.json';

			// FIND ON FILE
			if (file_exists($flightFile.$flightFilename)) {
				// IF FILE EXIST AND CHECK UPDATE
				$updateRequest = $url."ajax/mCheckFlightUpdated".$depature.$arrival.$dateGo.$dateReturn.$adult.$child.$infant.$key.$format;
				$getFlightUpdate = $this->Request_Model->httpGet($updateRequest);
				if($getFlightUpdate['status'] == 200) {

					$updateFlight = json_decode($getFlightUpdate['output'], true);

					if($updateFlight['update'] > 0) {
						$getFlightNew = $this->Request_Model->httpGet($newRequest);
						if($getFlightNew['status'] == 200) {
							if (write_file($flightFile.$flightFilename, $getFlightNew['output'])) {
								$flightsJson = $flightFileUrl.$flightFilename;
							} else {
								$res = array(
									'error' => 1,
									'datas' => 0,
									'msg' => 'Write file failed 91'				
								);
							}
						} else {
							$res = array(
								'error' => 1,
								'datas' => 0,
								'msg' => 'Disconnect from API 100'				
							);
						}
					} else {
						$flightsJson = $flightFileUrl.$flightFilename;
					}
				}
			}  else  {
		   		// IF FILE NOT EXIST
				$getFlightNew = $this->Request_Model->httpGet($newRequest);
				if($getFlightNew['status'] == 200) {
					if (write_file($flightFile.$flightFilename, $getFlightNew['output'])) {
						$flightsJson = $flightFileUrl.$flightFilename;
					} else {
						$res = array(
							'error' => 1,
							'datas' => 0,
							'msg' => 'Write file failed 119'				
						);
					}
				} else {
					$res = array(
						'error' => 1,
						'datas' => 0,
						'msg' => 'Disconnect from API 128'				
					);
				}
			}

			if(!isset($res)) {
				// FETCH JSON
				$jsonData = $this->Request_Model->httpGet($flightsJson);
				if($jsonData['status'] == 200) {

					$flightsData = json_decode($jsonData['output'], true);

					foreach ($flightsData['departures']['result'] as $key => $value) {
						foreach ($value['flight_infos']['flight_info'] as $k => $v) {
							// FIRST
							$findResult['departures'][$v['flight_number']]['flight_id'] = $value['flight_id'];
							$findResult['departures'][$v['flight_number']]['stop'] = $value['stop'];
							$findResult['departures'][$v['flight_number']]['airlines_name'] = $value['airlines_name'];
							// PRICE
							$findResult['departures'][$v['flight_number']]['price']['price_value'] = $value['price_value'];
							$findResult['departures'][$v['flight_number']]['price']['price_adult'] = $value['price_adult'];
							$findResult['departures'][$v['flight_number']]['price']['price_child'] = $value['price_child'];
							$findResult['departures'][$v['flight_number']]['price']['price_infant'] = $value['price_infant'];

							// SECOND
							// FROM
							$findResult['departures'][$v['flight_number']]['flight_number'] = $v['flight_number'];
							$findResult['departures'][$v['flight_number']]['departure_city'] = $v['departure_city'];
							$findResult['departures'][$v['flight_number']]['departure_city_name'] = $v['departure_city_name'];
							$findResult['departures'][$v['flight_number']]['departure_airport_name'] = $v['departure_airport_name'];
							$findResult['departures'][$v['flight_number']]['departure_airport_terminal'] = $v['terminal'];
							$findResult['departures'][$v['flight_number']]['departure_date_time'] = $v['departure_date_time'];
							// TO
							$findResult['departures'][$v['flight_number']]['arrival_city'] = $v['arrival_city'];
							$findResult['departures'][$v['flight_number']]['departure_city_name'] = $v['arrival_city_name'];
							$findResult['departures'][$v['flight_number']]['arrival_airport_name'] = $v['arrival_airport_name'];
							$findResult['departures'][$v['flight_number']]['arrival_date_time'] = $v['arrival_date_time'];
							// Thumbnail
							$findResult['departures'][$v['flight_number']]['img_src'] = $v['img_src'];
							// DURATION
							$findResult['departures'][$v['flight_number']]['duration_second'] = $v['duration_time'];
							$findResult['departures'][$v['flight_number']]['duration_minute'] = $v['duration_minute'];
							$findResult['departures'][$v['flight_number']]['duration_hour'] = $v['duration_hour'];
							// BAGASI
							$findResult['departures'][$v['flight_number']]['check_in_baggage'] = $v['check_in_baggage'];
							$findResult['departures'][$v['flight_number']]['check_in_baggage_unit'] = $v['check_in_baggage_unit'];
						}
					}

					if(!empty($this->input->get('returnDate')) || $this->input->get('returnDate') != '') {
						foreach ($flightsData['returns']['result'] as $key => $value) {
							foreach ($value['flight_infos']['flight_info'] as $k => $v) {
								// FIRST
								$findResult['returns'][$v['flight_number']]['flight_id'] = $value['flight_id'];
								$findResult['returns'][$v['flight_number']]['stop'] = $value['stop'];
								$findResult['returns'][$v['flight_number']]['airlines_name'] = $value['airlines_name'];
								// PRICE
								$findResult['returns'][$v['flight_number']]['price']['price_value'] = $value['price_value'];
								$findResult['returns'][$v['flight_number']]['price']['price_adult'] = $value['price_adult'];
								$findResult['returns'][$v['flight_number']]['price']['price_child'] = $value['price_child'];
								$findResult['returns'][$v['flight_number']]['price']['price_infant'] = $value['price_infant'];

								// SECOND
								// FROM
								$findResult['returns'][$v['flight_number']]['flight_number'] = $v['flight_number'];
								$findResult['returns'][$v['flight_number']]['departure_city'] = $v['departure_city'];
								$findResult['returns'][$v['flight_number']]['departure_city_name'] = $v['departure_city_name'];
								$findResult['returns'][$v['flight_number']]['departure_airport_name'] = $v['departure_airport_name'];
								$findResult['returns'][$v['flight_number']]['departure_airport_terminal'] = $v['terminal'];
								$findResult['returns'][$v['flight_number']]['departure_date_time'] = $v['departure_date_time'];
								// TO
								$findResult['returns'][$v['flight_number']]['arrival_city'] = $v['arrival_city'];
								$findResult['returns'][$v['flight_number']]['departure_city_name'] = $v['arrival_city_name'];
								$findResult['returns'][$v['flight_number']]['arrival_airport_name'] = $v['arrival_airport_name'];
								$findResult['returns'][$v['flight_number']]['arrival_date_time'] = $v['arrival_date_time'];
								// Thumbnail
								$findResult['returns'][$v['flight_number']]['img_src'] = $v['img_src'];
								// DURATION
								$findResult['returns'][$v['flight_number']]['duration_second'] = $v['duration_time'];
								$findResult['returns'][$v['flight_number']]['duration_minute'] = $v['duration_minute'];
								$findResult['returns'][$v['flight_number']]['duration_hour'] = $v['duration_hour'];
								// BAGASI
								$findResult['returns'][$v['flight_number']]['check_in_baggage'] = $v['check_in_baggage'];
								$findResult['returns'][$v['flight_number']]['check_in_baggage_unit'] = $v['check_in_baggage_unit'];
							}
						}
					}

					$res = array(
						'error' => 0,
						'datas' => $findResult,
						'msg' => 'Get data success'				
					);

					echo json_encode($res);

				} else {
					$res = array(
						'error' => 1,
						'datas' => 0,
						'msg' => 'Disconnect from API 229'				
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

	public function Get_Nearest_Airport()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('flight_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('flight_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('flight_token_session'));

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
			$key = "?token=".$this->session->userdata('flight_token_session');
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

				$res = array(
					'error' => 0,
					'datas' => $nearAirport,
					'msg' => 'Get near station data'				
				);

				echo json_encode($res);
			} else {
				$res = array(
					'error' => 1,
					'datas' => 0,
					'msg' => 'Disconnect from API 274'				
				);

				echo json_encode($res);
			}
		}
	}
	public function Get_Popular_Airport_Destination()
	{

	}

	public function Search_Airport()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('flight_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('flight_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('flight_token_session'));

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
		if(empty($this->session->userdata('flight_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('flight_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('flight_token_session'));

		if($validate_token) {
			$param = "flight_api/all_airport";
			$key = "?token=".$this->session->userdata('flight_token_session');
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
		if(empty($this->session->userdata('flight_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('flight_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('flight_token_session'));

		if($validate_token) {
			// TRUE
			$param = "flight_api/get_flight_data";
			$depatureId = "?flight_id=".$this->input->get('depatureId');
			$depatureDate = "&date=".$this->input->get('depatureDate');
			$returnId = "&ret_flight_id=".$this->input->get('returnId');
			$returnDate = "&ret_date=".$this->input->get('returnDate');
			$version = "&v=3";
			$key = "&token=".$this->session->userdata('flight_token_session');	
			$format = "&output=json";

			// $request = $url.$param.$depatureId.$depatureDate.$returnId.$returnDate.$version.$key.$format;
			$request = base_url().'files/flight/GetFlightData.json';

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$checkFlight = json_decode($getResponse['output'], true);

				// LION CAPTCHA
				// TRUE
				$paramCaptcha = "flight_api/getLionCaptcha";
				$keyCaptcha = "&token=".$this->session->userdata('flight_token_session');	
				$formatCaptcha = "&output=json";

				$requestCaptcha = $url.$paramCaptcha.$keyCaptcha.$formatCaptcha;
				// $requestCaptcha = base_url().'files/flight/LionCaptcha.json';

				$getResponseCaptcha = $this->Request_Model->httpGet($requestCaptcha);
				if($getResponseCaptcha['status'] == 200) {
					$getCaptcha = json_decode($getResponseCaptcha['output'], true);
					$this->session->set_userdata('lion_captcha_captcha', $getCaptcha['lioncaptcha']);
					$this->session->set_userdata('lion_captcha_id', $getCaptcha['lionsessionid']);

					// required separator
					$flightData['flight']['required']['input'] = array_keys($checkFlight['required']);
					$flightData['flight']['required']['data'] = array_values($checkFlight['required']);

					// departures
					$flightData['flight']['departures']['flightId'] = $checkFlight['departures']['flight_id'];
					$flightData['flight']['departures']['airlinesName'] = $checkFlight['departures']['airlines_name'];
					$flightData['flight']['departures']['flightDate'] = $checkFlight['departures']['flight_date'];
					$flightData['flight']['departures']['departureCity'] = $checkFlight['departures']['departure_city'];
					$flightData['flight']['departures']['arrivalCity'] = $checkFlight['departures']['arrival_city'];
					$flightData['flight']['departures']['stop'] = $checkFlight['departures']['stop'];
					$flightData['flight']['departures']['priceValue'] = $checkFlight['departures']['price_value'];
					$flightData['flight']['departures']['priceAdult'] = $checkFlight['departures']['price_adult'];
					$flightData['flight']['departures']['priceChild'] = $checkFlight['departures']['price_child'];
					$flightData['flight']['departures']['priceInfant'] = $checkFlight['departures']['price_infant'];
					$flightData['flight']['departures']['countAdult'] = $checkFlight['departures']['count_adult'];
					$flightData['flight']['departures']['countChild'] = $checkFlight['departures']['count_child'];
					$flightData['flight']['departures']['countInfant'] = $checkFlight['departures']['count_infant'];
					$flightData['flight']['departures']['timestamp'] = $checkFlight['departures']['timestamp'];
					$flightData['flight']['departures']['baggage'] = $checkFlight['departures']['check_in_baggage'];
					$flightData['flight']['departures']['needBaggage'] = $checkFlight['departures']['need_baggage'];
					$flightData['flight']['departures']['baggageUnit'] = $checkFlight['departures']['check_in_baggage_unit'];
					$flightData['flight']['departures']['departureTime'] = $checkFlight['departures']['simple_departure_time'];
					$flightData['flight']['departures']['departureDate'] = $checkFlight['departures']['departure_flight_date'];

					$flightData['flight']['departures']['arrivalTime'] = $checkFlight['departures']['simple_arrival_time'];
					$flightData['flight']['departures']['arrivalDate'] = $checkFlight['departures']['arrival_flight_date'];

					$flightData['flight']['departures']['departureCity'] = $checkFlight['departures']['departure_city_name'];
					$flightData['flight']['departures']['arrivalCity'] = $checkFlight['departures']['arrival_city_name'];
					$flightData['flight']['departures']['airportName'] = $checkFlight['departures']['departure_airport_name'];
					$flightData['flight']['departures']['arrivalAirport'] = $checkFlight['departures']['arrival_airport_name'];
					$flightData['flight']['departures']['duration'] = $checkFlight['departures']['duration'];
					$flightData['flight']['departures']['image'] = $checkFlight['departures']['image'];

					// departures flight_infos
					foreach ($checkFlight['departures']['flight_infos']['flight_info'] as $ky => $vl) {
						$flightData['flight']['departures']['flight_info'] = $vl;
					}

					if(isset($checkFlight['returns'])) {
						// returns
						$flightData['flight']['returns']['flightId'] = $checkFlight['returns']['flight_id'];
						$flightData['flight']['returns']['airlinesName'] = $checkFlight['returns']['airlines_name'];
						$flightData['flight']['returns']['flightDate'] = $checkFlight['returns']['flight_date'];
						$flightData['flight']['returns']['departureCity'] = $checkFlight['returns']['departure_city'];
						$flightData['flight']['returns']['arrivalCity'] = $checkFlight['returns']['arrival_city'];
						$flightData['flight']['returns']['stop'] = $checkFlight['returns']['stop'];
						$flightData['flight']['returns']['priceValue'] = $checkFlight['returns']['price_value'];
						$flightData['flight']['returns']['priceAdult'] = $checkFlight['returns']['price_adult'];
						$flightData['flight']['returns']['priceChild'] = $checkFlight['returns']['price_child'];
						$flightData['flight']['returns']['priceInfant'] = $checkFlight['returns']['price_infant'];
						$flightData['flight']['returns']['countAdult'] = $checkFlight['returns']['count_adult'];
						$flightData['flight']['returns']['countChild'] = $checkFlight['returns']['count_child'];
						$flightData['flight']['returns']['countInfant'] = $checkFlight['returns']['count_infant'];
						$flightData['flight']['returns']['timestamp'] = $checkFlight['returns']['timestamp'];
						$flightData['flight']['returns']['baggage'] = $checkFlight['returns']['check_in_baggage'];
						$flightData['flight']['returns']['needBaggage'] = $checkFlight['returns']['need_baggage'];
						$flightData['flight']['returns']['baggageUnit'] = $checkFlight['returns']['check_in_baggage_unit'];
						$flightData['flight']['returns']['departureTime'] = $checkFlight['returns']['simple_departure_time'];
						$flightData['flight']['returns']['departureDate'] = $checkFlight['returns']['departure_flight_date'];

						$flightData['flight']['returns']['arrivalTime'] = $checkFlight['returns']['simple_arrival_time'];
						$flightData['flight']['returns']['arrivalDate'] = $checkFlight['returns']['arrival_flight_date'];

						$flightData['flight']['returns']['departureCity'] = $checkFlight['returns']['departure_city_name'];
						$flightData['flight']['returns']['arrivalCity'] = $checkFlight['returns']['arrival_city_name'];
						$flightData['flight']['returns']['airportName'] = $checkFlight['returns']['departure_airport_name'];
						$flightData['flight']['returns']['arrivalAirport'] = $checkFlight['returns']['arrival_airport_name'];
						$flightData['flight']['returns']['duration'] = $checkFlight['returns']['duration'];
						$flightData['flight']['returns']['image'] = $checkFlight['returns']['image'];

						// departures flight_infos
						foreach ($checkFlight['returns']['flight_infos']['flight_info'] as $ky => $vl) {
							$flightData['flight']['returns']['flight_info'] = $vl;
						}
					}

					$res = array(
						'error' => 0,
						'datas' => $flightData,
						'msg' => 'Get flight data success'				
					);

					echo json_encode($res);
				} else {
					$res = array(
						'error' => 1,
						'datas' => 0,
						'msg' => 'Disconnect from API 548'				
					);

					echo json_encode($res);
				}
			} else {
				$res = array(
					'error' => 1,
					'datas' => 0,
					'msg' => 'Disconnect from API 557'				
				);

				echo json_encode($res);
			}
		}
	}

	public function Flight_Add_Order()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('flight_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('flight_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('flight_token_session'));

		if($validate_token) {
			// TRUE
			$param = "order/add/flight";
			$depatureId = "?flight_id=".$this->input->get('depatureId');
			$returnId = "&ret_flight_id=".$this->input->get('returnId');
			$lioncaptcha = "&lioncaptcha=".$this->session->userdata('lion_captcha_captcha');
			$lionsessionid = "&lionsessionid=".$this->session->userdata('lion_captcha_id');
			$adult = "&adult=".$this->input->get('adult');
			$child = "&child=".$this->input->get('child');
			$infant = "&infant=".$this->input->get('invant');
			$p = "&conSalutation=Mrs&conFirstName=budianto&conLastName=wijaya&conPhone=%2B6287880182218&conEmailAddress=you_julin@yahoo.com&conOtherPhone=%2B628521342534&firstnamea1=susi&lastnamea1=wijaya&birthdatea1=1990-02-09&ida1=&titlea1=Mr&firstnamec1=carreen&lastnamec1=athalia&birthdatec1=2005-02-02&idc1&titlei1=Mr&firstnamei1=wendy&lastnamei1=suprato&birthdatei1=2011-06-29&idi1&parenti1=&passportnoa1&passportExpiryDatea1=2020-09-02&passportissueddatea1=2015-0902&passportissuinga1&passportnationalitya1=id&passportnoc1&passportExpiryDatec1&passportissueddatec1&birthdatec1&passportissuingc1&passportnationalityc1&passportnoe1&passportExpiryDatee1&passportissueddatee1&birthdatee1&passportissuinge1&passportnationalitye1&dcheckinbaggagea11&dcheckinbaggagec11&dcheckinbaggagee11&rcheckinbaggagea11&rcheckinbaggagec11&rcheckinbaggagee11";
			$version = "&v=3";
			$key = "&token=".$this->session->userdata('flight_token_session');	
			$format = "&output=json";

			$request = $url.$param.$depatureId.$returnId.$lioncaptcha.$lionsessionid.$adult.$child.$infant.$p.$version.$key.$format;
			// $request = base_url().'files/flight/GetFlightData.json';

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$checkFlight = json_decode($getResponse['output'], true);

				if($checkFlight['diagnostic']['status'] > 200) {

					$paramOrder = "order";		
					$keyOrder = "&token=".$this->session->userdata('flight_token_session');
					$formatOrder = "&output=json";

					$requestOrder = $url.$paramOrder.$keyOrder.$formatOrder;
					
					$getResponseOrder = $this->Request_Model->httpGet($requestOrder);
					if($getResponseOrder['status'] == 200) {
						$getOrder = json_decode($getResponseOrder['output'], true);

						print_r($getResponseOrder); exit();

						// LIST
						$orderData[$getOrder['myorder']['order_id']]['orderId'] = $getOrder['myorder']['order_id'];

						// ORDER DATA
						foreach ($getOrder['myorder']['data'] as $kData => $vData) {
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['expire'] = $vData['expire'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderDetailId'] = $vData['order_detail_id'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderExpireDatetime'] = $vData['order_expire_datetime'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderType'] = $vData['order_type'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderName'] = $vData['order_name'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderNameDetail'] = $vData['order_name_detail'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderDetailStatus'] = $vData['order_detail_status'];
							// $orderData[$getOrder['myorder']['order_id']]['list'][$kData]['tenor'] = $vData['tenor'];

							// DETAIL
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderDetailId'] = $vData['detail']['order_detail_id'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['airlinesName'] = $vData['detail']['airlines_name'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['flightNumber'] = $vData['detail']['flight_number'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['priceAdult'] = $vData['detail']['price_adult'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['priceChild'] = $vData['detail']['price_child'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['priceInfant'] = $vData['detail']['price_infant'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['flightDate'] = $vData['detail']['flight_date'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['departureTime'] = $vData['detail']['departure_time'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['arrivalDate'] = $vData['detail']['flight_date'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['arrivalTime'] = $vData['detail']['arrival_time'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['baggageFee'] = $vData['detail']['baggage_fee'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['departureAirport'] = $vData['detail']['departure_airport_name'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['arrivalAirport'] = $vData['detail']['arrival_airport_name'];

							// passengers
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['passengers'] = $vData['detail']['passengers'];

							// PRICE
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['totalPrice'] = $vData['detail']['price'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['breakdownPrice'] =$vData['detail']['breakdown_price'];

							// ORDERS MANAGE
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['deleteOrder'] = $vData['delete_uri'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderPhoto'] = $vData['order_photo'];
						}

						// TOTAL
						$orderData[$getOrder['myorder']['order_id']]['total'] = $getOrder['myorder']['total'];
						$orderData[$getOrder['myorder']['order_id']]['totalTax'] = $getOrder['myorder']['total_tax'];
						$orderData[$getOrder['myorder']['order_id']]['totalWithoutTax'] = $getOrder['myorder']['total_without_tax'];
						$orderData[$getOrder['myorder']['order_id']]['countInstallment'] = $getOrder['myorder']['count_installment'];
						$orderData[$getOrder['myorder']['order_id']]['discount'] = $getOrder['myorder']['discount'];
						$orderData[$getOrder['myorder']['order_id']]['discount_amount'] = $getOrder['myorder']['discount_amount'];

						// CHECKOUT
						$orderData[$getOrder['myorder']['order_id']]['checkoutUrl'] = $getOrder['checkout'];

						$data = array(
							'error' => $getDetail['diagnostic']['status'],
							'msg' => 'Success get data',
							'data' => $orderData
						);

						echo json_encode($data);
					} else {
						$data = array(
							'error' => $getResponseOrder['status'],
							'msg' => 'Error '.$getResponseOrder['status'].' disconnect from API 677',
							'data' => 0
						);

						echo json_encode($data);
					}
				} else {
					$data = array(
						'error' => $checkFlight['diagnostic']['status'],
						'msg' => 'Error '.$checkFlight['diagnostic']['status'].' disconnect from API 686',
						'data' => 0
					);

					echo json_encode($data);
				}
			} else {
				$data = array(
					'error' => $getResponse['status'],
					'msg' => 'Error '.$getResponse['status'].' disconnect from API 695',
					'data' => 0
				);

				echo json_encode($data);
			}
		}
	}

	public function Flight_Delete_Order()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('flight_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('flight_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('flight_token_session'));

		if($validate_token) {
			// TRUE
			$urlDelete = $this->input->get('delete');		
			// $key = "&token=".$this->session->userdata('flight_token_session');
			$key = "&token=624cb009761ecadbd0042685a4a9d491f475b7df";
			$format = "&output=json";

			$request = $urlDelete.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$getDelete = json_decode($getResponse['output'], true);

				if($getDelete['diagnostic']['status'] == 200) {

					$data = array(
						'error' => 200,
						'msg' => $getDelete['updateStatus']
					);

					echo json_encode($data);
				} else {
					$data = array(
						'error' => $getDelete['diagnostic']['status'],
						'msg' => $getDelete['diagnostic']['error_msgs']
					);

					echo json_encode($data);
				}
			} else {
				$data = array(
					'error' => 500,
					'msg' => 'Error 500',
					'data' => 0
				);

				echo json_encode($data);
			}
		}
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
