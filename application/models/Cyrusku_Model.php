<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Cyrusku_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertTrx($command,$product,$msisdn,$trxid,$signature,$rs_result,$rs_msg,$rs_trxid,$dr_sn,$send_type)
    {
        $trx_datetime = date('Y-m-d H:i:s');

    	$sql = "INSERT INTO cyrusku_api_trx(command,product,msisdn,trxid,signature,rs_result,rs_msg,rs_trxid,dr_sn,send_type,trx_datetime) "
                . "VALUES("
                . "" . $this->db->escape($command) . ", "
                . "" . $this->db->escape($product) . ", "
                . "" . $this->db->escape($msisdn) . ", "
                . "" . $this->db->escape($trxid) . ", "
                . "" . $this->db->escape($signature) . ", "
                . "" . $this->db->escape($rs_result) . ", "
                . "" . $this->db->escape($rs_msg) . ", "
                . "" . $this->db->escape($rs_trxid) . ", "
                . "" . $this->db->escape($dr_sn) . ", "
                . "" . $this->db->escape($send_type) . ", "
                . "" . $this->db->escape($trx_datetime) . ")";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    public function updateSn($rs_trxid,$trxid,$dr_sn) {
        $sql = "UPDATE cyrusku_api_trx SET "
                . "dr_sn = " . $this->db->escape($dr_sn) . ""
                . " WHERE rs_trxid = " . $this->db->escape($trxid) . " AND trxid = " . $this->db->escape($trxid) . "";
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

