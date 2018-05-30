<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HotelAPI extends CI_Controller { 

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
		// Search Autocomplete API
		$token_tiket = $this->config->item('tiket_api_key');
		$validate_token = $this->Token_Model->validateToken($token_tiket);

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

					// PHOLICY
					$cPholicy = count($v['list_promo_policy']);
						$theHotel[$v['id']]['promo_policy']['promo_title_1'] = strip_tags($v['promo_title']);
					for($i=0; $i < $cPholicy; $i++) {
						$tier = array('tier_one','tier_two','tier_three','tier_four','tier_five');
						$j = $i + 1;
						$theHotel[$v['id']]['promo_policy']['promo_title_'.$j] = strip_tags($v['list_promo_policy'][$tier[$i]]);
					}

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
					
				if($getDetail['diagnostic']['status'] == 200) {
					// SUCCESS
					// Hotel_Order API
					$paramOrder = "order";		
					$keyOrder = "&token=".$this->session->userdata('hotel_token_session');
					$formatOrder = "&output=json";

					echo $requestOrder = $url.$paramOrder.$keyOrder.$formatOrder; exit();
					$getResponseOrder = $this->Request_Model->httpGet($requestOrder);
					if($getResponseOrder['status'] == 200) {
						$getOrder = json_decode($getResponseOrder['output'], true);
						foreach ($getOrder['myorder'] as $ky => $vl) {

							$orderData[$vl['order_id']]['orderId'] = $vl['order_id'];

							foreach ($vl['data'] as $kData => $vData) {
								$orderData[$vl['order_id']]['expire'] = $vData['expire'];
								$orderData[$vl['order_id']]['orderDetailId'] = $vData['order_detail_id'];
								$orderData[$vl['order_id']]['orderExpireDatetime'] = $vData['order_expire_datetime'];
								$orderData[$vl['order_id']]['orderType'] = $vData['order_type'];
								$orderData[$vl['order_id']]['orderName'] = $vData['order_name'];
								$orderData[$vl['order_id']]['orderNameDetail'] = $vData['order_name_detail'];
								$orderData[$vl['order_id']]['orderDetailStatus'] = $vData['order_detail_status'];
								$orderData[$vl['order_id']]['tenor'] = $vData['tenor'];

								// DETAILS
								foreach ($vData['detail'] as $kDetail => $vDetail) {
									$orderData[$vl['order_id']]['orderDetailId'] = $vDetail['order_detail_id'];
									$orderData[$vl['order_id']]['roomId'] = $vDetail['room_id'];
									$orderData[$vl['order_id']]['rooms'] = $vDetail['rooms'];
									$orderData[$vl['order_id']]['adult'] = $vDetail['adult'];
									$orderData[$vl['order_id']]['child'] = $vDetail['child'];
									$orderData[$vl['order_id']]['startdate'] = $vDetail['startdate'];
									$orderData[$vl['order_id']]['enddate'] = $vDetail['enddate'];
									$orderData[$vl['order_id']]['nights'] = $vDetail['nights'];
									$orderData[$vl['order_id']]['totalCharge'] = $vDetail['total_charge'];
									$orderData[$vl['order_id']]['startdateOriginal'] = $vDetail['startdate_original'];
									$orderData[$vl['order_id']]['enddateOriginal'] = $vDetail['enddate_original'];
									$orderData[$vl['order_id']]['price'] = $vDetail['price'];
									$orderData[$vl['order_id']]['pricePerNight'] = $vDetail['price_per_night'];
								}

								$orderData[$vl['order_id']]['orderPhoto'] = $vData['order_photo'];
								$orderData[$vl['order_id']]['tax'] = $vData['tax'];
								$orderData[$vl['order_id']]['itemCharge'] = $vData['item_charge'];
								$orderData[$vl['order_id']]['subtotalCharge'] = $vData['subtotal_and_charge'];

								// ORDERS MANAGE
								$orderData[$vl['order_id']]['deleteOrder'] = $vData['delete_uri'];
								$orderData[$vl['order_id']]['businessId'] = $vData['business_id'];
							}

							$orderData[$vl['order_id']]['total'] = $vl['total'];
							$orderData[$vl['order_id']]['totalTax'] = $vl['total_tax'];
							$orderData[$vl['order_id']]['totalWithoutTax'] = $vl['total_without_tax'];
							$orderData[$vl['order_id']]['countInstallment'] = $vl['count_installment'];

							$orderData[$vl['order_id']]['discount'] = $vl['discount'];
							$orderData[$vl['order_id']]['discount_amount'] = $vl['discount_amount'];
						}
					}

					foreach ($getDetail['checkout'] as $kCo => $vCo) {
						$orderData[$getDetail['diagnostic']['myorder']['order_id']]['checkout'] = $vCo['checkout'];
					}

				}
				
			}
		}
	}
	public function Hotel_Order()
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
			$param2 = "order";		
			$key2 = "&token=".$this->session->userdata('hotel_token_session');
			$format2 = "&output=json";
		}
	}

	public function Hotel_Checkout_Page_Request()
	{
		
	}
	public function Hotel_Checkout_Login()
	{

	}

	public function Hotel_Checkout_Customer()
	{
		
	}
	public function Hotel_Available()
	{

	}

	public function Hotel_Checkout_Payment()
	{
		
	}
}
