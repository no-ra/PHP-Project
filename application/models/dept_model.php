<?php

class Dept_Model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function tree_all(){
        $query = $this->db->query("SELECT id, name as text, parent FROM departments ORDER BY name");
        $result = $query->result_array();
        foreach ($result as $row) {
            if(!$row['parent']){
                $row['parent'] = '#';
            }
            $data[] = $row;
        }
        return $data;
    }

    public function get_user_dept($user_id) {
        $this->db->select('name');
        $this->db->from('user_view');
        $this->db->where('id', $user_id);
        $result = $this->db->get();
        return $result->row()->name;
    }

    public function get_departments() {
        $this->db->select('*');
        $this->db->from('departments');
        return $this->db->get()->result();
    }

    public function dept_user(){
        $this->db->select('name');
        $this->db->from('users');
        $this->db->join('departments', 'users.dept_id = departments.id');
        return $this->db->get()->result();
    }

    public function add_departments($data){
        $this->db->insert('departments',$data);
        return $this->db->insert_id();

    }
    public function delete_dept($id){
        $this->db->where('id', $id);
        $this->db->delete('departments');
    }


}

?>
