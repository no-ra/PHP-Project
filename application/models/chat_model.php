<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_model extends CI_Model {

	function add_message($message, $user_id, $guid)
	{
		$data = array(
			'message'	=> (string) $message,
			'user_id'	=> $user_id,
			'guid'		=> (string)	$guid,
			'timestamp'	=> time(),
		);

		$this->db->insert('messages', $data);
	}

	function get_messages($timestamp)
	{
	    $this->db->select('username,message,messages.id,guid,timestamp');
		$this->db->from('users');
		$this->db->join('messages','users.id = messages.user_id');
		$this->db->where('timestamp >', $timestamp);
		$this->db->order_by('timestamp', 'DESC');
		$this->db->limit(10);
		$query = $this->db->get();
		return array_reverse($query->result_array());
	}

}
?>
