
<?php

class User_Model extends CI_Model {

    var $table = 'users';
    var $column_order = array('username','first_name','last_name','address');
    var $column_search = array('first_name','last_name','address');

    public function __construct(){
        parent::__construct();
    }

    public function verify_user($username,$password){
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $query = $this->db->query($sql, [$username,$password]);
        if($query->num_rows() == 1) {
            return $query->row();
        } else {
            return null;
        }
    }

    public function update_user($id,$data){
        $this->db->where('id',$id);
        $this->db->update('users',$data);
    }

    private function _get_datatables_query() {
        $this->db->from('user_view');
        $i = 0;
        if(isset($_POST['search'])) {
            $value = $_POST['search']['value'];
            foreach ($this->column_search as $item) {
                if($i===0) {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $value);
                } else {
                    $this->db->or_like($item, $value);
                }

                if(count($this->column_search) - 1 == $i)
                    $this->db->group_end();
                $i++;
            }
        }
    }

    function get_datatables($dept_id)  {
        $this->_get_datatables_query();
        if($dept_id){
            $this->db->where('dept_id',$dept_id);
        }
        if($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();
            return $query->result();
    }

    function count_filtered(){
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->_get_datatables_query();
        return $this->db->count_all_results();
    }

    public function get_by_id($id){
        $this->db->from($this->table);
        $this->db->where('id',$id);
        $query = $this->db->get();
        return $query->row();
    }

    public function save($data)  {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function update($where, $data){
        $this->db->update($this->table,$data,$where);
        return $this->db->affected_rows();
    }

    public function delete_by_id($id) {
        $this->db->where('id',$id);
        $this->db->delete($this->table);
    }

}



 ?>
