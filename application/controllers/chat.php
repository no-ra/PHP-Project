<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {


	public function __construct(){
		parent::__construct();

		if(!isset($_SESSION['user'])) {
	      redirect('auth/');
	    }
	}


	public function index()
	{
		$data['username'] = $_SESSION['user']->username;
		$this->load->view('chat', $data);
	}
}
