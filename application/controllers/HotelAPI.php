<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HotelAPI extends CI_Controller { 

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

		$this->load->model('Hotel_Model'); 
	}

	public function index()
	{
		echo "Forbhident access : 404";
	}

	public function Search_Hotel()
	{
		echo "Try Hit but error <br>Fatal error: Allowed memory size of 572522496 bytes exhausted (tried to allocate 130968 bytes) in /var/www/vhosts/tiket.com/system/core/Input.php on line 3162";
	}
	public function Insert_Province()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$url = $this->config->item('tiket_api_url_dev');
			$param = "search/search_area";
			$uid = "?uid=country:id";
			$key = "&token=".$this->config->item('tiket_api_key');
			$format = "&output=json";

			$request = $url.$param.$uid.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);

			$allProvince = json_decode($getResponse, true);

			foreach ($allProvince['results']['result'] as $key => $value) {

				$checkProvince = $this->Hotel_Model->searchProvinceAPI($value['uid'],$value['value'],$value['uri']);

				if(count($checkProvince) == 0) {
					$this->Hotel_Model->insertNewProvince($value['uid'],$value['value'],$value['uri'],date('Y-m-d H:i:s'));
				}			
			}

			echo "OK";
		}
	}
	public function Search_Autocomplete()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$param = "search/autocomplete/hotel";
			$query = "?q=".$this->input->get('search');
			$key = "&token=".$this->session->userdata('hotel_token_session');
			$format = "&output=json";

			$request = $url.$param.$query.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);

			if($getResponse['status'] == 200) {

				$hotelName = json_decode($getResponse['output'], true);

				foreach ($hotelName['results']['result'] as $k => $v) {
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

					$findResult[$v['id']]['mkd'] = $this->session->userdata('hotel_token_session');							
					// $hotelName[$v['id']]['wifi'] = $v['wifi'];							
					// $hotelName[$v['id']]['promo_name'] = $v['promo_name'];							
					// $hotelName[$v['id']]['price'] = $v['price'];							
					// $hotelName[$v['id']]['total_price'] = $v['total_price'];					
				}

			echo json_encode($findResult);
			}			
		}
	}
	public function Search_By_Area()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$search = 'jakar';
			$find = $this->Hotel_Model->searchProvinceLike($search);

			if(count($find) > 0) {

				foreach ($find as $key => $value) {
					// GET ARE FROM PROVINCE
					$url = $this->config->item('tiket_api_url_dev');
					$param = "search/hotel";
					$uid = "?uid=".$value->kode_province;
					$key = "&token=".$this->config->item('tiket_api_key');
					$format = "&output=json";

					$request = $url.$param.$uid.$key.$format;

					$getResponse = $this->Request_Model->httpGet($request);

					$allProvince = json_decode($getResponse, true);

					foreach ($allProvince['results']['result'] as $k => $v) {
						$hotelProvince[$v['id']]['hotel_id'] = $v['hotel_id'];							
						$hotelProvince[$v['id']]['province_name'] = $v['province_name'];							
						$hotelProvince[$v['id']]['regional'] = $v['regional'];							
						$hotelProvince[$v['id']]['name'] = $v['name'];						
						$hotelProvince[$v['id']]['kecamatan_name'] = $v['kecamatan_name'];							
						$hotelProvince[$v['id']]['kelurahan_name'] = $v['kelurahan_name'];							
						$hotelProvince[$v['id']]['star_rating'] = $v['star_rating'];							
						$hotelProvince[$v['id']]['room_available'] = $v['room_available'];							
						$hotelProvince[$v['id']]['room_max_occupancies'] = $v['room_max_occupancies'];
						$hotelProvince[$v['id']]['rating'] = $v['rating'];
						$hotelProvince[$v['id']]['latitude'] = $v['latitude'];							
						$hotelProvince[$v['id']]['longitude'] = $v['longitude'];							
						$hotelProvince[$v['id']]['business_uri'] = base_url().'ViewDetailHotel?detail='.$v['business_uri'];							
						$hotelProvince[$v['id']]['photo_primary'] = $v['photo_primary'];							
						$hotelProvince[$v['id']]['address'] = strip_tags($v['address']);							
						$hotelProvince[$v['id']]['wifi'] = $v['wifi'];							
						$hotelProvince[$v['id']]['promo_name'] = $v['promo_name'];							
						$hotelProvince[$v['id']]['price'] = $v['price'];							
						$hotelProvince[$v['id']]['total_price'] = $v['total_price'];					
					}

					// PAGINATION
					$hotelProvince['pagination']['total_found'] = $allProvince['pagination']['total_found'];
					$hotelProvince['pagination']['current_page'] = $allProvince['pagination']['current_page'];
					$hotelProvince['pagination']['data_offset'] = $allProvince['pagination']['offset'];
					$hotelProvince['pagination']['page'] = $allProvince['pagination']['lastPage'];
				}

				echo json_encode($hotelProvince);
			}
		}
	}
	public function View_Detail_Hotel()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$urlDetail = $this->input->get('detail');
			$startdate = "&startdate=".$this->input->get('checkin');
			$night = "&night=".$this->input->get('night');
			$end = date('Y-m-d', strtotime($this->input->get('checkin') . ' +'.$this->input->get('night').' day'));
			$enddate = "&enddate=".$end;
			
			$room = "&room=".$this->input->get('room');
			$adult = "&adult=".$this->input->get('adult');
			$child = "&child=".$this->input->get('child');
			$infant = "&infant=".$this->input->get('invant');
			$key = "&token=".$this->session->userdata('hotel_token_session');
			$format = "&output=json";

			$request = $urlDetail.$startdate.$enddate.$night.$room.$adult.$child.$infant.$key.$format;


			$getResponse = $this->Request_Model->httpGet($request);

			$getDetail = json_decode($getResponse['output'], true); 

			if($getResponse['status'] == 200) {

				foreach ($getDetail['results']['result'] as $k => $v) {
					// BREADCUMB
					$theHotel[$v['id']]['business_id'] = $getDetail['breadcrumb']['business_id'];
					$theHotel[$v['id']]['business_name'] = $getDetail['breadcrumb']['business_name'];
					$theHotel[$v['id']]['lat'] = $getDetail['breadcrumb']['business_lat'];
					$theHotel[$v['id']]['long'] = $getDetail['breadcrumb']['business_long'];

					// ROOM LIST
					$theHotel[$v['id']]['room_id'] = $v['room_id'];
					$theHotel[$v['id']]['room_name'] = $v['room_name'];

					// // PHOLICY
					// $cPholicy = count($v['list_promo_policy']);
					// 	$theHotel[$v['id']]['promo_policy']['promo_title_1'] = strip_tags($v['promo_title']);
					// for($i=0; $i < $cPholicy; $i++) {
					// 	$tier = array('tier_one','tier_two','tier_three','tier_four','tier_five');
					// 	$j = $i + 1;
					// 	$theHotel[$v['id']]['promo_policy']['promo_title_'.$j] = strip_tags($v['list_promo_policy'][$tier[$i]]);
					// }

					// BOOK
					$theHotel[$v['id']]['bookUri'] = $v['bookUri'];
					$theHotel[$v['id']]['minimum_stays'] = $v['minimum_stays'];
					$theHotel[$v['id']]['with_breakfasts'] = $v['with_breakfasts'];
					$theHotel[$v['id']]['price'] = $v['price'];
					$theHotel[$v['id']]['currency'] = $v['currency'];

					// PHOTO
					$cPhoto = count($v['all_photo_room']);
					for($i=0; $i < $cPhoto; $i++) {
						$theHotel[$v['id']]['room_image']['room_image_'.$i] = $v['all_photo_room'][$i];
					}

					// ROOM FASILITY
					$cFasility = count($v['room_facility']);
					for($i=0; $i < $cFasility; $i++) {
						$theHotel[$v['id']]['room_facility']['room_facility_'.$i] = $v['room_facility'][$i];
					}

					$theHotel[$v['id']]['mkd'] = $this->session->userdata('hotel_token_session');
				}

				echo json_encode($theHotel);
			}
		}
	}
	public function Search_Hotel_Promo()
	{

	}
	public function Hotel_Add_Order()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) { 
			// TRUE
			$hotelData = $this->input->get('hotel');
			$hotelExpl = explode('_', $hotelData);

			$param = "order/add/hotel";
			$startdate = "?startdate=".$hotelExpl[1];			
			$end = date('Y-m-d', strtotime($hotelExpl[1] . ' +'.$hotelExpl[2].' day'));
			$enddate = "&enddate=".$end;
			$night = "&night=".$hotelExpl[2];
			$room = "&room=".$hotelExpl[6];

			$adult = "&adult=".$hotelExpl[3];
			$child = "&child=".$hotelExpl[4];
			$infant = "";

			$additional = "&minstar=0&maxstar=5&minprice=0&maxprice=1000&hotelname=0";
			$roomid = "&room_id=".$hotelExpl[0];			
			$key = "&hasPromo=0&token=".$this->session->userdata('hotel_token_session');
			$format = "&output=json";

			$request = $url.$param.$startdate.$enddate.$night.$room.$adult.$child.$infant.$additional.$roomid.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$getDetail = json_decode($getResponse['output'], true);
					
				if($getDetail['diagnostic']['status'] > 200) {
					// SUCCESS
					// Hotel_Order API
					$paramOrder = "order";		
					$keyOrder = "&token=".$this->session->userdata('hotel_token_session');
					$formatOrder = "&output=json";

					// $requestOrder = $url.$paramOrder.$keyOrder.$formatOrder;
					$requestOrder = "https://api-sandbox.tiket.com/order?token=624cb009761ecadbd0042685a4a9d491f475b7df&output=json";

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
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderName'] = $vData['order_name'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderNameDetail'] = $vData['order_name_detail'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderDetailStatus'] = $vData['order_detail_status'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['tenor'] = $vData['tenor'];

							// DETAIL
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderDetailId'] = $vData['detail']['order_detail_id'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['roomId'] = $vData['detail']['room_id'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['rooms'] = $vData['detail']['rooms'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['adult'] = $vData['detail']['adult'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['child'] = $vData['detail']['child'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['startdate'] = $vData['detail']['startdate'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['enddate'] = $vData['detail']['enddate'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['nights'] = $vData['detail']['nights'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['totalCharge'] = $vData['detail']['total_charge'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['startdateOriginal'] = $vData['detail']['startdate_original'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['enddateOriginal'] = $vData['detail']['enddate_original'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['price'] = $vData['detail']['price'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['pricePerNight'] = $vData['detail']['price_per_night'];

							// TAX
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['orderPhoto'] = $vData['order_photo'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['tax'] = $vData['tax'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['itemCharge'] = $vData['item_charge'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['subtotalCharge'] = $vData['subtotal_and_charge'];

							// ORDERS MANAGE
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['deleteOrder'] = $vData['delete_uri'];
							$orderData[$getOrder['myorder']['order_id']]['list'][$kData]['businessId'] = $vData['business_id'];
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
							'error' => 500,
							'msg' => 'Error 500',
							'data' => 0
						);

						echo json_encode($data);
					}
				} elseif($getDetail['diagnostic']['status'] == 211) {
					$data = array(
						'error' => $getDetail['diagnostic']['status'],
						'msg' => $getDetail['diagnostic']['error_msgs'],
						'data' => 0
					);

					echo json_encode($data);
				}
				
			}
		}
	}
	public function Hotel_Delete_Order()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$urlDelete = $this->input->get('delete');		
			$key = "&token=".$this->session->userdata('hotel_token_session');
			// $key = "&token=624cb009761ecadbd0042685a4a9d491f475b7df";
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
	public function Hotel_Checkout_Page_Request()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$urlCheckout = $this->input->get('checkout');		
			// $key = "&token=".$this->session->userdata('hotel_token_session');
			$key = "&token=624cb009761ecadbd0042685a4a9d491f475b7df";
			$format = "&output=json";

			$request = $urlCheckout.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {
				$checkoutCoustumer = json_decode($getResponse['output'], true);

				if($checkoutCoustumer['diagnostic']['status'] == 200) {

					$data = array(
						'error' => 200
					);

					echo json_encode($data);
				} else {
					$data = array(
						'error' => $checkoutCoustumer['diagnostic']['status']
					);

					echo json_encode($data);
				}
			}
		}
	}
	public function Hotel_Checkout_Login()
	{

	}

	public function Hotel_Checkout_Customer()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$parms = "checkout/checkout_customer";
			$salutation = "?salutation=".$this->input->get('salutation');
			$firstName = "&firstName=".$this->input->get('firstName');
			$lastName = "&lastName=".$this->input->get('lastName');
			$emailAddress = "&emailAddress=".$this->input->get('emailAddress');
			$phone = "&phone=".$this->input->get('phone');
			$conSalutation = "&conSalutation=".$this->input->get('conSalutation');
			$conFirstName = "&conFirstName=".$this->input->get('conFirstName');
			$conLastName = "&conLastName=".$this->input->get('conLastName');
			$conEmailAddress = "&conEmailAddress=".$this->input->get('conEmailAddress');
			$conPhone = "&conPhone=".$this->input->get('conPhone');
			$detailId = "&detailId=".$this->input->get('detailId');
			$country = "&country=".$this->input->get('country');
			// $key = "&token=".$this->session->userdata('hotel_token_session');
			$key = "&token=624cb009761ecadbd0042685a4a9d491f475b7df";
			$format = "&output=json";

			$request = $url.$parms.$salutation.$firstName.$lastName.$emailAddress.$phone.$conSalutation.$conFirstName.$conLastName.$conEmailAddress.$conPhone.$detailId.$country.$key.$format;

			$getResponse = $this->Request_Model->httpGet($request);
			if($getResponse['status'] == 200) {

				$hotelFile = FCPATH.'files/hotel/orders/';
				$hotelFileUrl = base_url().'files/hotel/orders/';
				if (!is_dir($hotelFile)) {
					mkdir($hotelFile, 0777, TRUE);
				}

				$hotelOrderFilename = $this->session->userdata('hotel_token_session').'.json';

				// FIND ON FILE
				if (!file_exists($hotelFile.$hotelOrderFilename)) {
					write_file($hotelFile.$hotelOrderFilename, $getResponse['output']);	
				}

				// CHECKOUT PAYMENT AVAILABLE API
				$param = "checkout/checkout_payment";
				// $requestPayment = $url.$param.$key.$format;
				$requestPayment = "http://localhost/abunawas/files/json-example/AvailablePayment.json";
				$getResponsePayment = $this->Request_Model->httpGet($requestPayment);
				$getPaymentAvailable = json_decode($getResponsePayment['output'], true);
					
				if($getResponsePayment['status'] == 200) {
					foreach ($getPaymentAvailable['available_payment'] as $kData => $vData) {
						$id = str_replace('/', '', substr($vData['link'],-2));
						$payAvailable[$id]['id'] = $id;
						$payAvailable[$id]['type'] = $vData['text'];						 
					}

					echo json_encode($payAvailable);
				}
			}
		}
	}
	public function Hotel_Checkout_Payment()
	{
		$url = $this->config->item('tiket_api_url_dev');
		if(empty($this->session->userdata('hotel_token_session'))) {
			$getToken = $url."apiv1/payexpress?method=getToken&secretkey=".$this->config->item('tiket_secret_key')."&output=json";
			$getTokenResponse = $this->Request_Model->httpGet($getToken);
			if($getTokenResponse['status'] == 200) {

				$parsetoken = json_decode($getTokenResponse['output'], true);
				$this->session->set_userdata('hotel_token_session', $parsetoken['token']);

			}
			
		}

		$validate_token = $this->Token_Model->validateToken($this->session->userdata('hotel_token_session'));

		if($validate_token) {
			// TRUE
			$paymentMethodCode = $this->input->get('code');

			if($paymentMethodCode == 2) {
				// Transfer Bank
				$methodeUrl = $url."checkout/checkout_payment/2?token=".$this->session->userdata('hotel_token_session')."&currency=IDR&btn_booking=1&output=json";
				// $methodeUrl = "http://localhost/abunawas/files/json-example/ForBankTransfer.json";

				$getResponse = $this->Request_Model->httpGet($methodeUrl);
				if($getResponse['status'] == 200) {
					$hotelPayment = json_decode($getResponse['output'], true);

					foreach($hotelPayment as $payment) {
						$paymentInfo[$payment['orderId']]['orderiId'] = $payment['orderId'];
						$paymentInfo[$payment['orderId']]['bank'] = $payment['banks'];
						$paymentInfo[$payment['orderId']]['message'] = $payment['message'];
						$paymentInfo[$payment['orderId']]['grandTotal'] = $payment['grand_total'];

						$hotelPaymentFile = FCPATH.'files/hotel/payments/';
						$hotelPaymentFileUrl = base_url().'files/hotel/payments/';
						if (!is_dir($hotelPaymentFile)) {
							mkdir($hotelPaymentFile, 0777, TRUE);
						}
						$hotelPaymentData = "payment-url-".$payment['orderId'].'.json';

						// FIND ON FILE
						if (!file_exists($hotelPaymentFile.$hotelPaymentData)) {
							write_file($hotelPaymentFile.$hotelPaymentData, $payment['confirm_payment']);	
						}
					}

					echo json_encode($paymentInfo);
				}
			} elseif ($paymentMethodCode == 3) {
				// Klik BCA
				// $methodeUrl = $url."checkout/checkout_payment/3?token=".$this->session->userdata('hotel_token_session')."&user_bca=".$this->input->get('klikbcaid')."&currency=IDR&btn_booking=1&output=json";

				$methodeUrl = "http://localhost/abunawas/files/json-example/ForKlikBCA.json";

				$getResponse = $this->Request_Model->httpGet($methodeUrl);
				if($getResponse['status'] == 200) {
					$hotelPayment = json_decode($getResponse['output'], true);

					foreach($hotelPayment as $payment) {
						$paymentInfo[$payment['orderId']]['orderiId'] = $payment['orderId'];
						$paymentInfo[$payment['orderId']]['message'] = $payment['message'];
						$paymentInfo[$payment['orderId']]['grandTotal'] = $payment['grand_total'];
					}

					echo json_encode($paymentInfo);
				}
			} elseif ($paymentMethodCode == 4) {
				// Transfer (instant confirmation)
				$methodeUrl = $url."payment/checkout_payment/?payment_type=4&token=".$this->session->userdata('hotel_token_session')."&output=json";
			} elseif ($paymentMethodCode == 20) {
				$methodeUrl = $url."checkout/checkout_payment/2?token=".$this->session->userdata('hotel_token_session')."&currency=IDR&btn_booking=1&output=json";
			}
		}
	}
}
