<?php

class Logged{

    public static function check(){

        $id = Session::exists('id') ? Session::get('id') : false;
        $email = Session::exists('email') ? Session::get('email') : false;
        $username = Session::exists('username') ? Session::get('username') : false;

        if($id && ($email || $username)){
            return true;
        }else{
            return false;
        }
    }

    public static function in(){
        if(self::check()){
            return true;
        }else{
            return false ;
        }
    } 

    public static function out(){
        if(!self::check()){
            return true;
        }else{
            return false ;
        }
     }

    public static function link(){
        Redirect::to('logout');
    }

    public static function inRedirect(){
        if(!self::check()){
            self::link();
        }
    }

    public static function outRedirect(){
        if(self::check()){
            self::link();
        }
    }

    public function login($data){
        if(is_object($data) || is_array($data)){
			foreach($data as $key => $value){
				$_SESSION[$key] = $value;
			}
		}
    }

    public function logout($data){
        if(is_object($data) || is_array($data)){
			foreach($data as $key => $value){
				unset($_SESSION[$$key]);
			}
		}
    }

}
?>