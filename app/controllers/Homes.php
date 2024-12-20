<?php
class Homes extends Controller{

    private $userModel;
    private $db;

    public function __construct(){
        $this->userModel = $this->model('User');
        $this->db = new Database;
    }

    public function index(){
        $data = [
            'page' => 'Home'
        ];
        $this->view('homes/index', $data);
    }

    public function login(){
        Logged::outRedirect();
        $data = (object)true;
        $err = (object)true;

        $err->email = '';
        $err->password = '';
        $err->cred = '';
        $data->err  = $err;

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data->email = trim($_POST['email']);
            $data->password = trim($_POST['password']);
            $err->cred = $err->email = !empty(Errors::checkPassword($data->email, $data->password)) ? Errors::checkPassword($data->email, $data->password) :  $err->cred;
            $err->email = !empty(Errors::email($data->email)) ? Errors::email($data->email) :  $err->email;
            $err->email = !empty(Errors::empty($data->email)) ? Errors::empty($data->email) :  $err->email;
            $err->password = !empty(Errors::password($data->password)) ? Errors::password($data->password) :  $err->password;

            if(empty($err->email) && empty($err->password)){
                $login = $this->userModel->login($data);
                if($login){
                    unset($login->password);
                    foreach($login as $key=>$value){
                        Session::set($key, $value);        
                    }
                    Redirect::to('/welcome', $login->email);
                }
            }

            $this->view('homes/login', $data, $err);
        }else{
            $data->email = '';
            $data->password = '';
            $this->view('homes/login', $data, $err);
        }
    }

    public function register($url){
        Logged::outRedirect();
        $data = (object)true;
        $err = (object)true;
        $err->name = '';
        $err->email = '';
        $err->password = '';
        $err->confirm_password = '';
        $data->err = $err;

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            $data->name = trim($_POST['name']);
            $data->email = trim($_POST['email']);
            $data->password = trim($_POST['password']);
            $data->confirm_password = trim($_POST['confirm_password']);
            $err->name = !empty(Errors::empty($data->name)) ? Errors::empty($data->name) :  $err->name;
            $err->email = !empty(Errors::checkEmail($data->email)) ? Errors::checkEmail($data->email) :  $err->email;
            $err->email = !empty(Errors::email($data->email)) ? Errors::email($data->email) :  $err->email;
            $err->password = $err->confirm_error = !empty(Errors::match([$data->password, $data->confirm_password])) ? Errors::match([$data->password, $data->confirm_password]): $err->password = $err->confirm_password ;
            $err->password = !empty(Errors::password($data->password)) ? Errors::password($data->password) :  $err->password;
            $err->password = !empty(Errors::min(8, $data->password)) ? Errors::min(8, $data->password) :  $err->password;
            $err->confirm_password = !empty(Errors::empty($data->confirm_password)) ? Errors::empty($data->confirm_password) :  $err->confirm_password;

            if(empty($err->name) && empty($err->email) && empty($err->password) && empty($err->confirm_error)){
                $data->password = password_hash($data->password, PASSWORD_DEFAULT);
                if($this->userModel->register($data)){
                    Session::set('registered', 'You are registered and can login');
                    Redirect::to('/login');
                }else{
                    Redirect::to('/error404');
                }
            }
            $this->view('homes/register', $data, $err);
        }else{
            $data->name = '';
            $data->email = '';
            $data->password = '';
            $data->confirm_password = '';
            $this->view('homes/register', $data, $err);
        }
    }

    public function about($url){
        $data = [
            'page' => 'About'
        ];
        $this->view('homes/about', $data);
    }

    public function error404(){
        $this->view('homes/error404');
    }

    public function welcome($email){
        Logged::inRedirect();
        $data = $this->userModel->findUserById(Session::get('id'));
        if($data->email != $email[0]){
            Redirect::to('/logout');
        }
        $this->view('homes/welcome', $data);
    }

    public function logout(){
        $data = $this->userModel->findUserById(Session::get('id'));
        foreach($data as $key=>$value){
            Session::delete($key);  
        }
        Redirect::to('');
    }

    public function profile($email){
        $email = $this->userModel->findUserByEmail($email[0]);
        if(empty($email)){
            Redirect::to('/error404');
        }else{
            $data = $email;
            $this->view('homes/profile', $data);
        }
    }

    public function test(){

        $test = $this->db->searchField('users', 'name', 'uche'); 
        echo '<pre>';
        print_r($test);

    }
}
?>