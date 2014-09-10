<?php

class twitter_helper extends helper_core {
	private $sdk = null;
	
	public function login($callback = null, $logined = null) {
		$consumer = array(
			"key" => "",
			"secret" => "",
						  );
		App::import("twitteroauth");
		if(isset($_SESSION["oauth_token"]) && isset($_SESSION["oauth_token_secret"])) {
			$this->sdk = new TwitterOAuth($consumer["key"], $consumer["secret"], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			$_SESSION['access_token'] = $this->sdk->getAccessToken($_REQUEST['oauth_verifier']);
			unset($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		}
		if(isset($_SESSION['access_token'])) {
			$this->sdk = new TwitterOAuth($consumer["key"], $consumer["secret"], $_SESSION['access_token']['oauth_token'], $_SESSION['access_token']['oauth_token_secret']);
			call_user_func($logined, $this->sdk);
		} else {
			$this->sdk = new TwitterOAuth($consumer["key"], $consumer["secret"]);
			$request_token = $this->sdk->getRequestToken($callback);
			$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
			$url = $this->sdk->getAuthorizeURL($token);
			App::redirect($url);
		}
	}

	public function __call($name, $param){
		if(is_callable(array($this->sdk, $name))) {
			try {
				return call_user_func_array(array($this->sdk, $name), $param);
			}catch(Exception $e) {
				if(config::fetch("environment") === "develop") {
					throw $e;
				} else {
					return false;
				}
			}
		} else {
			parent::__call($name, $param);
		}
	}
	
}