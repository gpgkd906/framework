<?php 

class account_controller extends application {
	
	protected function before_action() {
		if(!$this->auth->is_valid() && !is_callable(array($this, $this->get_action()))) {
			$this->route->redirect("account/login");
		}
		parent::before_action();
	}

	public function logout() {
		$this->auth->logout();
		$this->route->redirect("account/login");
	}
	
	public function login() {
		$this->auth->use_model($this->account);
		$this->auth->valid_flag("status", "valid");
		$login = $this->auth->make_login(function($login) {
				$login->account->class("form-control")->placeholder("account");
				$login->password->class("form-control")->placeholder("password");
				$login->submit->class("btn btn-lg btn-success btn-block")->value("ログイン");
			});
		$this->auth->login_handler(function($auth) {
				$this->route->redirect("admin");
			}, function($error) {
				$this->set("error", $error);
			});
		$this->assign(get_defined_vars());
	}

	public function facebook_login() {
		$this->account->facebook_login(function($account, $facebook) {
				$this->route->redirect("index");
			}, function($facebook) {
				$this->route->redirect("account/facebook_register");
			});
	}
	
	public function facebook_register() {
		$facebook = App::helper("facebook")->api("/me");
		$email = $name = "";
		if(isset($facebook["email"])) {
			$email = $facebook["email"];
		}
		if(isset($facebook["name"])) {
			$name = $facebook["name"];
		}
		$reg = $this->account->register($email, $name, function($data, $id) use($facebook) {
				//App::model("account_meta")->update_meta($id, "facebook_id", $facebook["id"]);
				$this->_register_complete($data);
			});
		$this->assign(get_defined_vars());
	}

	public function api_facebook_login() {
		$this->account->facebook_login(function($account, $facebook) {
				App::redirect("account/api_facebook_logined?status=authorized&account_id={$account->id}&token={$account->token}");
			}, function($facebook) {
				if(isset($facebook["id"])) {
					$data = array(
						"account" => $facebook["email"],
						"password" => "",
						"facebook_id" => $facebook["id"]
								  );
					$id = $this->auth->register($data); 
					App::model("profiles")->create_record(array("account_id" => $id, "name" => $facebook["name"], "nickname" => $facebook["name"]));
					App::model("alerms")->init($id);
					$record = App::model("account")->find_by_id($id);
					App::redirect("account/api_facebook_logined?status=authorized&account_id={$id}&token={$record->token}");
				} else {
					App::redirect("account/api_facebook_logined?status=error");					
				}
				

			});		
	}
	public function api_facebook_logined() {}

	public function api_twitter_login() {
		$this->account->twitter_login(config::fetch("www") . "account/api_twitter_login", function($account, $twitter) {
				App::redirect("account/api_twitter_logined?status=authorized&account_id={$account->id}&token={$account->token}");				
			}, function($twitter) {
				if(isset($twitter->error)) {
					App::redirect("account/api_twitter_logined?status=false");
				}
				if(isset($twitter->id_str)) {
					$data = array(
						"account" => "",
						"password" => "",
						"twitter_id" => $twitter->id
								  );
					$id = $this->auth->register($data); 
					App::model("profiles")->create_record(array("account_id" => $id, "name" => $twitter->name, "nickname" => $twitter->screen_name));
					App::model("alerms")->init($id);
					$record = App::model("account")->find_by_id($id);
					App::redirect("account/api_twitter_logined?status=authorized&account_id={$id}&token={$record->token}");
				} else {
					App::redirect("account/api_twitter_logined?status=error");					
				}
			});
	}
	public function api_twitter_logined() {}

	public function twitter_login() {
		$this->account->twitter_login(config::fetch("www") . "account/twitter_login", function($account, $twitter) {
				$this->route->redirect("index");				
			}, function($twitter) {
				$reg = $this->account->register("", $twitter->name, function($data, $id) use($twitter) {
						//App::model("account_meta")->update_meta($id, "twitter_id", $twitter->id);
						$this->_register_complete($data);
					});	
				$this->set("reg", $reg);
				$this->set_template("twitter_register");
			});		
		$this->assign(get_defined_vars());
	}

	public function twitter_register() {
		$this->account->twitter_login(config::fetch("www") . "account/twitter_login", function() {
				$this->route->redirect("index");				
			}, function() {
				$this->route->redirect("account/twitter_register");				
			});
	}

	public function add($id = null, $salt = null) {
		if(empty($id) || empty($salt)) {
			$this->route->redirect("account/error");
		}
		$add = $this->account->add($id, $salt, function($data, $account) {
				$this->_register_complete($data);
			});
		if(!$add) {
			$this->route->redirect("account/error");
		}
		$this->assign(get_defined_vars());
	}

	private function _register_complete($data) {
		App::helper("auth")->login($data["mail"], $data["password"]);
		App::helper("mail")->send($data,
			join(PHP_EOL, array()),
			$data["mail"]);
		$this->route->redirect("account/complete");
	}

	public function reset_password($old_token) {
		App::helper("auth")->reset_password_by_token($old_token);
	}

	public function complete() {
		$this->assign(get_defined_vars());
	}

	public function error() {
		$this->assign(get_defined_vars());
	}

	
	
}