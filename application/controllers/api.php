<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {


	public function __construct()
	{
		parent::__construct();

		if(!isset($_SESSION['user'])) {
	      redirect('auth/');
	    }

		$this->load->model('Chat_model');
	}

	public function send_message()
	{
		$message = $this->input->get('message', null);
		$user_id = $_SESSION['user']->id;
		$guid = $this->input->get('guid', '');

		$this->Chat_model->add_message($message, $user_id, $guid);

		$this->_setOutput($message);
	}


	public function get_messages()
	{
		$timestamp = $this->input->get('timestamp', null);
		$messages = $this->Chat_model->get_messages($timestamp);
		$this->_setOutput($messages);
	}


	private function _setOutput($data)
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');

		echo json_encode($data);
	}
}
?>
