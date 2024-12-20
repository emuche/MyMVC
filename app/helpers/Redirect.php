<?php

class Redirect{

	public static function to($location = '/index', $params = []){
                
                if(($location == 'index') || ($location == 'home') || ($location == '')){
                        $location = '';
                }elseif($location == '404'){
                        $location = 'home/error404';
                }

                $url = '';
                $params = (array)$params;
                if(count($params) > 0){
                        foreach($params as $param){
                                if(!empty($param)){
                                        $url .='/'.str_replace(' ','-', $param);
                                }else{
                                        $url = '';
                                }
                        }
                }

                header('location: '.URLROOT.$location.$url);
                exit();
	}

        public static function link($location = '/index', $params = []){

                if(($location == 'index') || ($location == 'home') || ($location == '') ){
                        $location = '';
                }elseif($location == '404'){
                        $location = 'home/error404';
                }	

                $url = '';
                $params = (array)$params;
                if(count($params) > 0){
                        foreach($params as $param){
                                if(!empty($param)){
                                        $url .='/'.str_replace(' ','-', $param);
                                }else{
                                        $url = '';
                                }
                        }
                }

                return URLROOT.$location.$url;
        }

        public static function param($param = null){
                if(empty($param)){
                        self::to('error404');
                }

        }

}

?>