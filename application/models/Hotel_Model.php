<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Hotel_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertNewProvince($kode_province,$region,$province_uri,$insert_datetime)
    {
    	$sql = "INSERT INTO tiket_api_hotel_province(kode_province,region,province_uri,insert_datetime) "
                . "VALUES("
                . "" . $this->db->escape($kode_province) . ", "
                . "" . $this->db->escape($region) . ", "
                . "" . $this->db->escape($province_uri) . ", "
                . "" . $this->db->escape($insert_datetime) . ")";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function updateProvincet($kode_province,$region,$province_uri,$update_datetime) {
        $sql = "UPDATE tiket_api_hotel_province SET "
                . "region = " . $this->db->escape($region) . ", "
                . "province_uri = " . $this->db->escape($province_uri) . ", "
                . "update_datetime = " . $this->db->escape($update_datetime) . ""
                . " WHERE kode_province = " . $this->db->escape($kode_province) . "";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function searchProvinceAPI($kode_province,$region,$province_uri)
    {
    	$sql = "SELECT * FROM tiket_api_hotel_province WHERE kode_province = " . $this->db->escape($kode_province) . " AND region = " . $this->db->escape($region) . " AND province_uri = " . $this->db->escape($province_uri) . "";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function searchProvinceLike($search)
    {
    	$sql = "SELECT * FROM tiket_api_hotel_province WHERE kode_province LIKE '%".$search."%' OR region LIKE '%".$search."%' OR province_uri LIKE '%".$search."%' ORDER BY region ASC LIMIT 1";
        $query = $this->db->query($sql);
        return $query->result();
    }

}

