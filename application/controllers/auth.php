<?php

class Auth extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    function index() {
        $this->load->view('login');
    }


    function login(){
        $header['title'] = "Login Page";
        $this->load->view('inc/header',$header);

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $this->load->model('user_model');

        if(empty($username) || empty($password)) {
            $error_msg = "Username dhe Password duhen plotesuar!";
            $this->load->view('login', ['error_msg' => $error_msg] );
            return;
        }

        $user = $this->user_model->verify_user($username,$password);

        if($user != null) {
            $_SESSION['user'] = $user;
            if($user->admin == 1){
                redirect('admin');
            } else {
                redirect('user');
            }
        } else {
            $error_msg = "Username/Password i gabuar!";
        }
        $this->load->view('login',['error_msg' => $error_msg] );
        $this->load->view('inc/footer');
    }

    function logout(){
        session_destroy();
        redirect(base_url());
    }
}


 ?>
