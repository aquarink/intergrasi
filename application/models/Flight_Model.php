<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Flight_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertNewAirport($airport_code,$airport_name,$country_id,$country_name,$input_datetime)
    {
    	$sql = "INSERT INTO tiket_api_flight_airports(airport_code,airport_name,country_id,country_name,input_datetime) "
                . "VALUES("
                . "" . $this->db->escape($airport_code) . ", "
                . "" . $this->db->escape($airport_name) . ", "
                . "" . $this->db->escape($country_id) . ", "
                . "" . $this->db->escape($country_name) . ", "
                . "" . $this->db->escape($input_datetime) . ")";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function insertFlightUpdate($update_token,$depature_code,$arrival_code,$update_date,$adult,$child,$infant,$timestmp,$filename)
    {
        $update_datetime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO tiket_api_flight_update(update_token,depature_code,arrival_code,update_date,adult,child,infant,timestmp,filename,update_datetime) "
                . "VALUES("
                . "" . $this->db->escape($update_token) . ", "
                . "" . $this->db->escape($depature_code) . ", "
                . "" . $this->db->escape($arrival_code) . ", "
                . "" . $this->db->escape($update_date) . ", "
                . "" . $this->db->escape($adult) . ", "
                . "" . $this->db->escape($child) . ", "
                . "" . $this->db->escape($infant) . ", "
                . "" . $this->db->escape($timestmp) . ", "
                . "" . $this->db->escape($filename) . ", "
                . "" . $this->db->escape($update_datetime) . ")";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function updateAirport($airport_code,$airport_name,$country_id,$country_name,$update_datetime) {
        $sql = "UPDATE tiket_api_flight_airports SET "
                . "airport_name = " . $this->db->escape($airport_name) . ", "
                . "country_id = " . $this->db->escape($country_id) . ", "
                . "country_name = " . $this->db->escape($country_name) . ", "
                . "update_datetime = " . $this->db->escape($update_datetime) . ""
                . " WHERE airport_code = " . $this->db->escape($airport_code) . "";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function searchAirportAPI($airport_code,$airport_name,$country_id,$country_name)
    {
    	$sql = "SELECT * FROM tiket_api_flight_airports WHERE airport_code = " . $this->db->escape($airport_code) . " AND airport_name = " . $this->db->escape($airport_name) . " AND country_id = " . $this->db->escape($country_id) . " AND country_name = " . $this->db->escape($country_name) . "";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function searchAirportLike($search) 
    {
    	$sql = "SELECT * FROM tiket_api_flight_airports WHERE airport_code LIKE '%".$search."%' OR airport_name LIKE '%".$search."%' OR country_name LIKE '%".$search."%' ORDER BY airport_name ASC";
        $query = $this->db->query($sql);
        return $query->result();
    }

}

