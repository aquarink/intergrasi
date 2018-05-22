<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['login'] = 'welcome/Login';
$route['logout'] = 'welcome/Logout';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;



// Flight API
$route['SearchFlight'] = 'FlightAPI/Search_Flight';
$route['GetNearestAirport'] = 'FlightAPI/Get_Nearest_Airport';
$route['GetPopularAirportDestination'] = 'FlightAPI/Get_Popular_Airport_Destination';
$route['SearchAirport'] = 'FlightAPI/Search_Airport';
$route['InsertAirport'] = 'FlightAPI/Insert_Airport';
$route['CheckUpdate'] = 'FlightAPI/Check_Update';
$route['GetLionCaptcha'] = 'FlightAPI/Get_Lion_Captcha';
$route['GetFlightData'] = 'FlightAPI/Get_Flight_Data';
$route['FlightAddOrder'] = 'FlightAPI/Flight_Add_Order';
$route['FlightOrder'] = 'FlightAPI/Flight_Order';
$route['DeleteOrder'] = 'FlightAPI/Delete_Order';
$route['FlightCheckoutPageRequest'] = 'FlightAPI/Flight_Checkout_Page_Request';
$route['FlightCheckoutLogin'] = 'FlightAPI/Flight_Checkout_Login';
$route['FlightCheckoutCostumer'] = 'FlightAPI/Flight_Checkout_Costumer';
$route['FlightAvailablePayment'] = 'FlightAPI/Flight_Available_Payment';
$route['FlightCheckoutPayment'] = 'FlightAPI/Flight_Checkout_Payment';

// Hotel API
$route['SearchHotel'] = 'HotelAPI/Search_Hotel';
$route['InsertProvince'] = 'HotelAPI/Insert_Province';
$route['SearchByArea'] = 'HotelAPI/Search_By_Area';
$route['SearchByName'] = 'HotelAPI/Search_By_Name';
$route['SearchAutocomplete'] = 'HotelAPI/Search_Autocomplete';
$route['SearchHotelPromo'] = 'HotelAPI/Search_Hotel_Promo';
$route['ViewDetailHotel'] = 'HotelAPI/View_Detail_Hotel';
$route['HotelAddOrder'] = 'HotelAPI/Hotel_Add_Order';
$route['HotelOrder'] = 'HotelAPI/Hotel_Order';
$route['HotelCheckoutPageRequest'] = 'HotelAPI/Hotel_Checkout_Page_Request';
$route['HotelCheckoutLogin'] = 'HotelAPI/Hotel_Checkout_Login';
$route['HotelCheckoutCustomer'] = 'HotelAPI/Hotel_Checkout_Customer';
$route['HotelAvailable ayment'] = 'HotelAPI/Hotel_Available';
$route['HotelCheckoutPayment'] = 'HotelAPI/Hotel_Checkout_Payment';

// Train API
$route['SearchTrain'] = 'TrainAPI/Search_Train';
$route['InsertStation'] = 'TrainAPI/Insert_Station';
$route['SearchStation'] = 'TrainAPI/Search_Station';
$route['GetTrainSeatMap'] = 'TrainAPI/Get_Train_Seat_Map';
$route['TrainAddOrder'] = 'TrainAPI/Train_Add_Order';
$route['TrainOrder'] = 'TrainAPI/Train_Order';
$route['TrainCheckoutPageRequest'] = 'TrainAPI/Train_Checkout_Page_Request';
$route['TrainCheckoutLogin'] = 'TrainAPI/Train_Checkout_Login';
$route['TrainAvailablePayment'] = 'TrainAPI/Train_Available_Payment';
$route['TrainCheckoutPayment'] = 'TrainAPI/Train_Checkout_Payment';
$route['TrainRegister'] = 'TrainAPI/Train_Register';
$route['TrainSearchPromo'] = 'TrainAPI/Train_Search_Promo';
$route['TrainChangeTrainSeat'] = 'TrainAPI/Train_Change_TrainSeat';

// Train API
$route['topup'] = 'CyruskuAPI/Top_Up';

