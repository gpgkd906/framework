<?php

class amazon_aws {
	private $aws = null;
	
	private $info = array(
		"aws_api_key" => "",
		"aws_api_secret_key" => "",
		"aws_associate_tag" => "",
		"aws_another_associate_tag" => "",
		"endpoint" => "co.jp",
						  );
	
	public function __construct() {
		if(!class_exists("AmazonECS")) {
			require "lib/AmazonECS.class.php";
		}
		return $this;
	}
	
	public function api_key($api_key) {
		$this->info["aws_api_key"] = $api_key;
		return $this;
	}
	
	public function secret_key($secret_key) {
		$this->info["aws_api_secret_key"] = $secret_key;		
		return $this;
	}

	public function associate_tag($associate_tag) {
		$this->info["aws_associate_tag"] = $associate_tag;
		return $this;
	}

	public function endpoint($endpoint) {
		$this->info["end_point"] = $endpoint;
		if($this->aws !== null) {
			$this->aws->country($endpoint);
		}
		return $this;
	}

	public function config($key, $value) {
		$this->info[$key] = $value;
		return $this;
	}
	
	public function __call($method, $param) {
		if($this->aws === null) {
			$this->make_instance();
		}
		return call_user_func_array(array($this->aws, $method), $param);
	}
	
	public function make_instance() {
		$this->aws = new AmazonECS(
			$this->info["aws_api_key"],
			$this->info["aws_api_secret_key"],
			$this->info["endpoint"],
			$this->info["aws_associate_tag"]
								   );
		return $this;
	}
	
}