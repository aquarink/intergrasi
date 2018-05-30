<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
	}

	public function index()
	{
		if($this->input->post()) {

		}

		$this->load->view('pesawat');
	}
	public function pesawat()
	{
		if($this->input->post()) {

		}

		$this->load->view('pesawat');
	}
	public function keretaapi()
	{
		if($this->input->post()) {

		}

		$this->load->view('keretaapi');
	}
	public function hotel()
	{
		if($this->input->post()) {

		}

		$this->load->view('hotel');
	}

	public function Login()
	{
		if($this->input->post()) {

			if(empty($this->input->post('email')) || empty($this->input->post('password'))) {
				redirect('login?error=Empty Field');
			} else {
				if($this->input->post('email') == 'admin@abunawas.com' AND $this->input->post('password') == 'abunawas') {
					redirect('login?error=0');
				} else {
					redirect('login?error=Email or Password wrong');
				}
			}

		}
		
		$this->load->view('user/login');
	}

	public function Logout()
	{

	}
}
