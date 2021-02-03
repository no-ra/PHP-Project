<?php

class Admin extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('dept_model');
        $this->load->model('user_model','user');
        if(!isset($_SESSION['user']) || $_SESSION['user']->admin == 0) {
          redirect('auth/');
        }
    }

    public function index(){
        $data['departments'] = $this->dept_model->get_departments();
        $this->load->view('admin/admin_home', $data);
    }

    public function ajax_list($dept_id = null) {
        $list = $this->user->get_datatables($dept_id);
        $data = array();
        $no = $this->input->post('start');

        foreach ($list as $user) {
            $no++;
            $row = array();
            $row[] = $user->username;
            $row[] = $user->first_name;
            $row[] = $user->last_name;
            $row[] = $user->address;
            if($user->admin == 1){
               $user->admin = 'admin';
            } else {
                $user->admin = 'user';
            }
            $row[] = $user->admin;
            $row[] = $user->name; //deptartment name
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_person('."'".$user->id."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
            <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_person('."'".$user->id."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $this->input->post('draw'),
            "recordsTotal" => $this->user->count_all(),
            "recordsFiltered" => $this->user->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function ajax_edit($id){
        $data = $this->user->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add() {
        $this->_validate();
        if($this->input->post()){
            $data = array(
               'username' => $this->input->post('username'),
               'first_name' => $this->input->post('first_name'),
               'last_name' => $this->input->post('last_name'),
               'password' => $this->input->post('password'),
               'address' => $this->input->post('address'),
               'admin' => $this->input->post('admin'),
               'dept_id' => $this->input->post('dept'),
            );
            $insert = $this->user->save($data);
            echo json_encode(array("status" => TRUE));
        } elseif ($this->input->get()) {
            $data = array(
                'username' => $this->input->get('username'),
                'first_name' => $this->input->get('first_name'),
                'last_name' => $this->input->get('last_name'),
                'password' => $this->input->get('password'),
                'address' => $this->input->get('address'),
                'admin' => $this->input->get('admin'),
                'dept_id' => $this->input->get('dept'),
            );
            $insert = $this->user->save($data);
            echo json_encode(array("status" => TRUE));
        }
    }

    public function ajax_update(){
        $this->_validate();
        $data = array(
           'username' => $this->input->post('username'),
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'password' => $this->input->post('password'),
            'address' => $this->input->post('address'),
            'admin' => $this->input->post('admin'),
            'dept_id' => $this->input->post('dept'),
        );
        $this->user->update(array('id' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_delete($id)  {
        $this->user->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    private function _validate(){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($this->input->post('username') == '') {
            $data['inputerror'][] = 'username';
            $data['error_string'][] = 'Username is required';
            $data['status'] = FALSE;
        }

        if($this->input->post('first_name') == '') {
            $data['inputerror'][] = 'first_name';
            $data['error_string'][] = 'First Name is required';
            $data['status'] = FALSE;
        }

        if($this->input->post('last_name') == '') {
            $data['inputerror'][] = 'last_name';
            $data['error_string'][] = 'Last Name is required';
            $data['status'] = FALSE;
        }

        if($this->input->post('admin') == '') {
            $data['inputerror'][] = 'admin';
            $data['error_string'][] = 'Role is required';
            $data['status'] = FALSE;
        }

        if($this->input->post('password') == '') {
            $data['inputerror'][] = 'password';
            $data['error_string'][] = 'Password is required';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    function test() {
        $data = $this->dept_model->tree_all();
        echo json_encode($data);
    }

    public function getChildren() {
        $result = $this->dept_model->tree_all();
        echo json_encode($result);
    }


    public function add_dept(){
        if($this->input->post()){
            $data = array(
                'name' => $this->input->post('name_dept'),
                'parent' =>$this->input->post('dept'),
            );
            $insert = $this->dept_model->add_departments($data);
            echo json_encode(array("status" => TRUE));
        } elseif ($this->input->get()) {
            $data = array(
                'name' => $this->input->get('name_dept'),
                'parent' =>$this->input->get('dept'),
            );
            $insert = $this->dept_model->add_departments($data);
            echo json_encode(array("status" => TRUE));
        }

    }
    function deleteDept($name){
        $data = $this->dept_model->delete_dept($id);
        echo json_encode($id);
    }

}



?>
