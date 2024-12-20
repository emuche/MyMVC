<?php
class Errors extends Controller{


    public function db(){
        return new Database;
    }


    public function models($model){
        return $this->model($model);
    }

    public static function checkEmail($email){
        $check = new Errors;
        $check = $check->models('User')->findUserByEmail($email);
        if(!empty($check)){
            return "Email already exist";
        }
    }

    public static function checkPassword($email, $password){
        $check = new Errors;
        $check = $check->models('User')->findUserByEmail($email);
        if(!empty($check)){
            $check = password_verify($password, $check->password);
            if(!$check){
                return "Credetials do not match";
            }
        }else{
            return "Credetials do not match";
        }
    }

    public static function confirmEmail($email){
        $check = new Errors;
        $check = $check->models('User')->findUserByEmail($email);
        if(empty($check)){
            return "Email does not exist";
        }
    }

    public static function empty($field){
        if(empty($field)){
            return "Field cannot be empty";
        }
    }

    public static function match($fields = []){
        if(count($fields) == 2){
            if(empty($fields[0]) || empty($fields[1]) || ($fields[0] !== $fields[1])){
                return "Fields do not match";
            }
        }else{
            return 'unforseen error';
        }
    }

    public static function password($pass){
       if(empty($pass) || !preg_match('/[A-Z]/', $pass) || !preg_match('~[0-9]+~', $pass )|| !preg_match('/[a-z]/', $pass) || !preg_match('/[\'^£$%&*()}{@#~>?<>!,|=_+¬-]/', $pass)){
            return 'Password must be atleast 8 characters, contain number, uppercase, lowercase and special character';
        }
    }

    public static function min($min, $field){
        if(strlen($field) < (int)$min){
            return "Field should not be less than $min Charaters";
        }
    }

    public static function max($max, $field){
        if(strlen($field) > (int)$max){
            return "Field should not be more than $max Charaters";
        }
    }

    public static function email($email){
        if(!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($email)){
            return 'Enter a valid email address';
        }
    }
}