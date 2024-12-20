<?php

class CSRF{

	public static function generate(){
		$tokenName = 'csrf_token';
		$token = Session::set($tokenName, md5(uniqid()));

		return '<input type="hidden" name="'.$tokenName.'" value="'.$token.'">';
	}

	public static function check($token){
		$tokenName = 'csrf_token';
		if(Session::exists($tokenName) && $token === Session::get($tokenName)){
			Session::delete($tokenName);
			return true;
		}else{
			Redirect::to('error404');
			return false;
		}
	}
}
?>