<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Token_Model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function validateToken($token) {

        return true;
    }

}

