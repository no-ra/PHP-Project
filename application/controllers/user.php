<?php

class User extends CI_Controller{

  public function __construct(){
    parent::__construct();
    if(!isset($_SESSION['user'])) {
      redirect('auth/');
    }
    $this->load->model('dept_model');
    $this->load->model('user_model');
  }

  public function test($id) {
      echo $this->dept_model->get_user_dept($id);
  }

  public function index(){
    $header['title'] = "User Page";
    $this->load->view("inc/header", $header);

    $data['user'] = $_SESSION['user'];
    $data['dept'] = $this->dept_model->get_user_dept($_SESSION['user']->id);
    $this->load->view("user/user_home", $data);

    $this->load->view("inc/footer");
  }

  public function get_current_user() {
    $id = $_SESSION['user']->id;
    $this->load->model('user_model');
    echo json_encode($this->user_model->get_by_id($id));
  }

  public function edit_current_user(){
    $this->_validate();
    error_log("edit_current_user: validation success!");
    $data = array(
      'first_name' => $this->input->post('first_name'),
      'last_name' => $this->input->post('last_name'),
      'address' => $this->input->post('address'),
      'password' => $this->input->post('password'),
    );

    if(!empty($_FILES['photo']['name'])) {
        $photo_name = $this->_do_upload();
        //delete file
        $user = $_SESSION['user'];
        // if($user->photo && file_exists('upload/'.$user->photo))
        //     unlink('upload/'.$user->photo);
        $data['photo'] = $photo_name;
        $_SESSION['user']->photo = $photo_name;
    }

    $id = $_SESSION['user']->id;
    $this->load->model('user_model');
    $this->user_model->update_user($id, $data);
    $user_data = $this->user_model->get_by_id($id);
    $user_data->status = TRUE;

    echo json_encode($user_data);

  }
  private function _validate(){
      $data = array();
      $data['error_string'] = array();
      $data['inputerror'] = array();
      $data['status'] = TRUE;

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


    private function _do_upload(){
        error_log("_do_upload: called");
        $config['upload_path']    = 'upload/';
        $config['allowed_types']  = 'gif|jpg|png|jpeg';
        $config['max_size']       = 100; //set max size allowed in Kilobyte
        $config['max_width']      = 1000; // set max width image allowed
        $config['max_height']     = 1000; // set max height allowed
        $config['file_name']      = round(microtime(true) * 1000); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config);
        error_log("_do_upload: upload module loaded");
        if(!$this->upload->do_upload('photo')) {//upload and validate
            $data['inputerror'][] = 'photo';
            $data['error_string'][] = 'Upload error: '.$this->upload->display_errors('',''); //show ajax error
            $data['status'] = FALSE;
            echo json_encode($data);
            exit();
        }
        error_log("_do_upload: success");
        return $this->upload->data('file_name');
    }


}



 ?>
