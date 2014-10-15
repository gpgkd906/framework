<?php

class facebook_helper extends helper_core {
	private $sdk = null;
	
	public function __construct(){
		App::import("facebook");
		$this->sdk = new Facebook(array(
				"appId" => "",
				"secret" => ""
										));
	}
	
	public function __call($name, $param){
		if(is_callable(array($this->sdk, $name))) {
			try {
				return call_user_func_array(array($this->sdk, $name), $param);
			}catch(Exception $e) {
				if(Config::fetch("environment") === "develop") {
					throw $e;
				} else {
					return false;
				}
			}
		} else {
			parent::__call($name, $param);
		}
	}

	public function get_shares($url = null) {
		if(empty($url)) {
			return 0;
		}
		$api_call = "https://api.facebook.com/method/fql.query?query=select%20like_count,%20total_count,%20share_count,%20click_count%20from%20link_stat%20where%20url='{$url}'";
		$response = file_get_contents($api_call);
		$res = App::module("xml")->load($response)->fetch("array");
		return $res["link_stat"]["like_count"];
	}
	
}
