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
		$token_tiket = $this->config->item('tiket_api_key');
		$validate_token = $this->Token_Model->validateToken($token_tiket);

		if($validate_token) {
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
	public function Search_By_Name()
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
					$param = "search/autocomplete/hotel";
					$query = "?q=mah";
					$key = "&token=".$this->config->item('tiket_api_key');
					$format = "&output=json";

					$request = $url.$param.$query.$key.$format;

					$getResponse = $this->Request_Model->httpGet($request);

					$hotelName = json_decode($getResponse, true);

					foreach ($hotelName['results']['result'] as $k => $v) {
						// $hotelName[$v['id']]['hotel_id'] = $v['hotel_id'];					
						// $hotelName[$v['id']]['province_name'] = $v['province_name'];							
						// $hotelName[$v['id']]['regional'] = $v['regional'];							
						$hotelName[$v['id']]['name'] = $v['value'];						
						// $hotelName[$v['id']]['kecamatan_name'] = $v['kecamatan_name'];							
						// $hotelName[$v['id']]['kelurahan_name'] = $v['kelurahan_name'];							
						// $hotelName[$v['id']]['star_rating'] = $v['star_rating'];							
						// $hotelName[$v['id']]['room_available'] = $v['room_available'];							
						// $hotelName[$v['id']]['room_max_occupancies'] = $v['room_max_occupancies'];
						// $hotelName[$v['id']]['rating'] = $v['rating'];
						// $hotelName[$v['id']]['latitude'] = $v['latitude'];							
						// $hotelName[$v['id']]['longitude'] = $v['longitude'];							
						$hotelName[$v['id']]['business_uri'] = base_url().'ViewDetailHotel?detail='.$v['business_uri'];
						// $hotelName[$v['id']]['photo_primary'] = $v['photo_primary'];						
						$hotelName[$v['id']]['address'] = strip_tags($v['label_location']);							
						// $hotelName[$v['id']]['wifi'] = $v['wifi'];							
						// $hotelName[$v['id']]['promo_name'] = $v['promo_name'];							
						// $hotelName[$v['id']]['price'] = $v['price'];							
						// $hotelName[$v['id']]['total_price'] = $v['total_price'];					
					}
				}

				echo json_encode($hotelName);
			}
		}
	}
	// FUNCTION SAMA DENGAN FUNCTION Search_By_Name DIATAS
	public function Search_Autocomplete()
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
					$param = "search/autocomplete/hotel";
					$query = "?q=mah";
					$key = "&token=".$this->config->item('tiket_api_key');
					$format = "&output=json";

					$request = $url.$param.$query.$key.$format;

					$getResponse = $this->Request_Model->httpGet($request);

					$hotelName = json_decode($getResponse, true);

					foreach ($hotelName['results']['result'] as $k => $v) {
						// $hotelName[$v['id']]['hotel_id'] = $v['hotel_id'];					
						// $hotelName[$v['id']]['province_name'] = $v['province_name'];							
						// $hotelName[$v['id']]['regional'] = $v['regional'];							
						$hotelName[$v['id']]['name'] = $v['value'];						
						// $hotelName[$v['id']]['kecamatan_name'] = $v['kecamatan_name'];							
						// $hotelName[$v['id']]['kelurahan_name'] = $v['kelurahan_name'];							
						// $hotelName[$v['id']]['star_rating'] = $v['star_rating'];							
						// $hotelName[$v['id']]['room_available'] = $v['room_available'];							
						// $hotelName[$v['id']]['room_max_occupancies'] = $v['room_max_occupancies'];
						// $hotelName[$v['id']]['rating'] = $v['rating'];
						// $hotelName[$v['id']]['latitude'] = $v['latitude'];							
						// $hotelName[$v['id']]['longitude'] = $v['longitude'];							
						$hotelName[$v['id']]['business_uri'] = base_url().'ViewDetailHotel?detail='.$v['business_uri'];
						// $hotelName[$v['id']]['photo_primary'] = $v['photo_primary'];						
						$hotelName[$v['id']]['address'] = strip_tags($v['label_location']);							
						// $hotelName[$v['id']]['wifi'] = $v['wifi'];							
						// $hotelName[$v['id']]['promo_name'] = $v['promo_name'];							
						// $hotelName[$v['id']]['price'] = $v['price'];							
						// $hotelName[$v['id']]['total_price'] = $v['total_price'];					
					}
				}

				echo json_encode($hotelName);
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
		$token_tiket = $this->config->item('tiket_api_key');
		$validate_token = $this->Token_Model->validateToken($token_tiket);

		if($validate_token) {
			// TRUE
			$url = $this->input->get('detail');
			$key = "&token=".$this->config->item('tiket_api_key');
			$format = "&output=json";

			$request = preg_replace('/\\\\/', '', $url.$key.$format);


			$getResponse = $this->Request_Model->httpGet($request);

			$getDetail = json_decode($getResponse, true);

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
			}

			echo json_encode($theHotel);
		}
	}
	public function Search_Hotel_Promo()
	{

	}
	public function Hotel_Add_Order()
	{
		
	}
	public function Hotel_Order()
	{

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
