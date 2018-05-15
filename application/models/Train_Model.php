<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Train_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertNewStation($station_code,$station_name,$station_city,$station_type,$station_longitude,$station_latitude,$input_datetime)
    {
    	$sql = "INSERT INTO tiket_api_train_station(station_code,station_name,station_city,station_type,station_longitude,station_latitude,input_datetime) "
                . "VALUES("
                . "" . $this->db->escape($station_code) . ", "
                . "" . $this->db->escape($station_name) . ", "
                . "" . $this->db->escape($station_city) . ", "
                . "" . $this->db->escape($station_type) . ", "
                . "" . $this->db->escape($station_longitude) . ", "
                . "" . $this->db->escape($station_latitude) . ", "
                . "" . $this->db->escape($input_datetime) . ")";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function updateStation($station_code,$station_name,$station_city,$station_type,$station_longitude,$station_latitude,$update_datetime) {
        $sql = "UPDATE tiket_api_train_station SET "
                . "station_name = " . $this->db->escape($station_name) . ", "
                . "station_city = " . $this->db->escape($station_city) . ", "
                . "station_type = " . $this->db->escape($station_type) . ", "
                . "station_longitude = " . $this->db->escape($station_longitude) . ", "
                . "station_latitude = " . $this->db->escape($station_latitude) . ", "
                . "update_datetime = " . $this->db->escape($update_datetime) . ""
                . " WHERE station_code = " . $this->db->escape($station_code) . "";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function searchStationAPI($station_code,$station_name,$station_city)
    {
    	$sql = "SELECT * FROM tiket_api_train_station WHERE station_code = " . $this->db->escape($station_code) . " AND station_name = " . $this->db->escape($station_name) . " AND station_city = " . $this->db->escape($station_city) . "";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function searchStationLike($search)
    {
    	$sql = "SELECT * FROM tiket_api_train_station WHERE station_code LIKE '%".$search."%' OR station_name LIKE '%".$search."%' OR station_city LIKE '%".$search."%' ORDER BY station_name ASC LIMIT 1";
        $query = $this->db->query($sql);
        return $query->result();
    }

}

