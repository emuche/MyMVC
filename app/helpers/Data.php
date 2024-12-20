<?php
class Data{

    public static function date($date){
        return date('dS M, Y',strtotime($date));
    }

    public static function cap($text){
        return ucwords($text);  
    }

    public static function is_multi_dim($data){
        if(is_array($data)){
            $is_array = array_filter($data, 'is_array');
            if(count($is_array)){
                return true;
            }

        }else{
            return false;
        }

    }

    public static function is_email($email){
        if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            return false;
        }
    }

}
?>