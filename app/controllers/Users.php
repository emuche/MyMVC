<?php
class Users extends Controller{
    private $db;
    private $userModel;


    public function __construct(){
        Logged::inRedirect();
        $this->db = new Database;
        $this->userModel = $this->model('User');
        
    }

    public function deleteUser($id){
        

    }

}