<?php

class auth_helper extends helper_core {

	private $target = null;

	private $hash = "sha256";

	private $model = null;

	private $key_column = "account"; 

	private $password_column = "password"; 

	private $error = array();

	private $valid_flag = array();

	private $reg = null;

	private $login = null;

	private $permission_handler = "self::default_permission_handler";
	
	public function __construct() {

		if(isset($_SESSION["auth_target"])) {

			$this->target = $_SESSION["auth_target"];

		}
		App::import("checker");

	}

	public function __destruct() {

		if(isset($this->target)) {

			$_SESSION["auth_target"] = $this->target;

		}

	}

	public function use_model($model, $key_column = "account", $password_column = "password") {

		$this->model = $model;

		$this->key_column = $key_column;

		$this->password_column = $password_column;

	}
	
	public function use_hash($hash = "sha256") {

		$enabled = hash_algos();

		if(in_array($hash, $enabled)) {

			$this->hash = $hash;

		}

	}
	
	public function valid_flag($col, $val) {

		$this->valid_flag = array(

			"col" => $col,

			"val" => $val

		);

	}
	
	public function hash($val, $salt = null) {

		if($salt !== null) {

			$val = $val . $salt;

		}

		return hash($this->hash, hash($this->hash, $val));

	}
	
	public function login($key = null, $password = null) {

		if(empty($key) && isset($_POST[$this->key_column])) {

			$key = $_POST[$this->key_column];

		}

		if(empty($password) && isset($_POST[$this->password_column])) {

			$password = $_POST[$this->password_column];

		}

		//exists check

		$error = array();

		if(empty($key)) {

			$error[$this->key_column] = "アカウントを入力してください";

		}

		if(empty($password)) {

			$error[$this->password_column] = "パスワードを入力してください";

		}

		$this->error = $error;

		if(empty($this->error) && $this->model() !== null) {

			$this->_login($key, $password);

		}

	}
	
	private function _login($key, $password) {

		$flag = $this->valid_flag;

		$this->model()->find($this->key_column, $key);

		if(!empty($flag)) {

			$this->model->find($flag["col"], $flag["val"]);

		}

		$this->model->skip_relation();

		$target = $this->model->get();



		if(!$target) {

			$this->error["login"] = "アカウントかパスワードが間違っています、確認の上再認証ください";

		} else {

			$hash_password = $this->hash($password, $target->salt);
			if(function_exists("hash_equals")) {
				$compare = hash_equals($hash_password, $target->password);
			} else {
				$hp = str_split($hash_password);
				$tp = str_split($target->password);
				$compare = (array_diff($hp, $tp) === array_diff($tp, $hp));
			}
			if($compare) {
				//ログイン時間を更新する
				$target->update_dt = $_SERVER["REQUEST_TIME"];
				//tokenも更新する
				$target->token = $this->generate_token($target->salt);
				
				$this->set_target($target->to_array());

				$target->save();

			} else {

				$this->error["login"] = "アカウントかパスワードが間違っています、確認の上再認証ください";

			}

		}

	}

	public function generate_token($seed) {

		return md5($seed . $_SERVER["REQUEST_TIME"]);

	}

	public function generate_password() {

		//小文字のlとお文字のOが混乱を避けるため、存在はしない

		$alphabet = "abcdefghijkmnopqrstuwxyzABCDEFGHIJKLMNPQRSTUWXYZ0123456789";

		$pass = array(); 

		$alphaLength = strlen($alphabet) - 1; 

		for ($i = 0; $i < 8; $i++) {

			$n = rand(0, $alphaLength);

			$pass[] = $alphabet[$n];

		}

		return implode($pass); 

	}

	public function token_authorization($token = null) {

		if(empty($token)) {

			return $this->target = null;

		}

		if(empty($this->target) || empty($this->target["token"])) {

			$this->_token_auth($token);

		}

		if($this->target["token"] !== $token) {

			$this->target = null;

		}

		return $this->target;

	}

	private function _token_auth($token) {

		$this->model()->skip_relation();

		if($target = $this->model->find_by_token($token)) {

			$this->set_target($target->to_array());

		}

	}
	
	private function set_target($target) {
		//まだ実装は先に送ったけど
		//セキュリティ的にIPアドレスを厳密にチェックする場合は対処可能に
		$target["REMOTE_ADDR"] = $_SERVER["REMOTE_ADDR"];

		$this->target = $target;

		$_SESSION["auth_target"] = $target;

	}
	
	/**
	 * backward compatibility
	 */
	public function set_user($user) {

		$this->set_target($user);

	}

	public function set_extend($extend) {

		if(!is_array($extend)) {

			return false;

		}

		$target = array_merge($extend, $this->target);

		$this->set_target($target);

	}

	public function set($key, $value) {

		if(isset($this->target[$key])) {

			$this->target[$key] = $value;

		}

		$this->set_target($this->target);

	}
	
	public function get_user() {

		return $this->target;

	}
	
	public function get_error() {

		$error = false;

		if(!empty($this->error)) {

			$error = $this->error;

		}

		return $error;

	}
	
	public function logout() {

		if (isset($_COOKIE[session_name()])) {

			setcookie(session_name(),'',time()-60*60*24*365, '/');

		}

		$_SESSION=array();

		session_unset();

		session_destroy();

	}
	
	public function __get($name) {

		if(isset($this->target[$name])) {

			return $this->{$name} = $this->target[$name];

		}

	}
	
	public function is_valid() {

		if($this->target == null) {

			$this->login();

		}

		return (boolean) $this->target;
	}
	
	public function make_login($customize = null) {

		if(!isset($this->reg)) {

			$reg = App::module("form2")->create("auth_login");

			$reg->add_text("account")->must_be(checker::Exists);

			$reg->add_password("password")->must_be(checker::Exists);

			$this->reg = $reg;

		}

		if(isset($customize) && is_callable($customize)) {

			call_user_func($customize, $this->reg, $this);

		}

		return $this->reg;	  

	}
	
	public function login_handler($submit_handler = null, $error_handler = null) {

		$account = $this->model;

		$auth = $this;

		$this->reg->submit(function($data, $reg) use ($account, $auth, $submit_handler, $error_handler) {

				$auth->login($data["account"], $data["password"]);

				if($auth->is_valid()){

					if(is_callable($submit_handler)) {

						call_user_func($submit_handler, $auth);

					}

				} else {

					if(is_callable($error_handler)) {

						call_user_func($error_handler, $this->error, $auth);			  

					}

				}

			});

	}
	
	//use form && model
	public function make_register($customize = null) {

		if(!isset($this->reg)) {

			$reg = App::module("form2")->create("register");

			$reg->add_text("account")->must_be(checker::Exists);

			$reg->add_password("password")->must_be(checker::Exists);

			$reg->add_password("password2")->must_be(checker::Exists);

			$this->reg = $reg;

		}

		if(isset($customize) && is_callable($customize)) {

			call_user_func($customize, $this->reg, $this);

		}

		return $this->reg;

	}

	public function register_handler($complete_handler = null, $error_handler = null) {

		$account = $this->model;

		$auth = $this;

		$this->reg->submit(function($data, $reg) use($account) {

				if($data["password"] !== $data["password2"]) {

					$reg->password2->force_error("パスワードは一致してません");
				}

				$checker = $account->find_by_account($data["account"]);

				if($checker) {

					$reg->account->force_error("※該当アカウントは既に存在しました");

				}

			});

		$this->reg->confirm(null, function($data, $reg) use($account, $auth, $complete_handler, $error_handler) {

				$id = $auth->register($data);

				$data["account_id"] = $id;

				$auth->login($data["account"], $data["password2"]);

				if($auth->is_valid() ){

					if(is_callable($complete_handler)) {

						call_user_func($complete_handler, $data, $auth);

					}

				} else {

					if(is_callable($error_handler)) {

						call_user_func($error_handler, $this->error, $auth);			  

					}

				}

			});

	}
	
	public function register($data, $permission = "user") {

		if(empty($this->model)) {

			$this->model = App::model("account");

		}

		$record = $this->pre_register($data);

		$record->permission = $permission;

		$id = $record->save();

		if($id == 1) {

			$record->permission = "system";

			$record->save();

		}

		return $id;

	}

	public function pre_register($data) {

		$data["salt"] = uniqid();

		$data["password"] = $this->hash($data["password"], $data["salt"]);

		$data["token"] = $this->generate_token($data["salt"]);

		$record = $this->model->new_record();

		$record->assign($data);

		$record->status = "valid";

		return $record;

	}

	public function update_account($account_id, $account = null, $password = null) {

		if($account === null && $password === null) {

			return false;

		}

		$record = $this->model->find_by_id($account_id);

		if(!empty($account)) {

			$record->account = $account;

		}

		if(!empty($password)) {

			//古いパスワードと比較する

			$new_password = $this->hash($password, $record->salt);

			if($new_password !== $record->password) {

				//セキュリティ上の考慮から、パスワードを更新する時、saltやtokenも更新する

				$record->salt = uniqid();

				$record->token = $this->generate_token($record->salt);

				//saltは新しく生成しましたので、パスワードをもう一回ハッシュしないといけない

				$record->password = $this->hash($password, $record->salt);

			}

		}

		return $record->save();

	}

	public function reset_password_by_token($token) {

		return $this->reset_password($this->model()->find_by_token($token));

	}


	public function reset_password_by_id($id) {

		return $this->reset_password($this->model()->find_by_id($id));

	}

	public function reset_password_by_request($request) {

		if($request && isset($request["token"])) {

			return $this->reset_password($this->model()->find_by_token($request["token"]));		

		}

		return false;

	}



	public function get_user_by_request($request) {

		if($request && isset($request["token"])) {

			return $this->model()->find_by_token($request["token"]);

		}

		return false;

	}



	private function reset_password($account) {

		if(empty($account)) {

			return false;

		}

		$new_password = App::helper("auth")->generate_password();

		$this->update_account($account->id, null, $new_password);

		return $new_password;

	}



	public function generate_request_key($account = null) {

		if($account !== null) {

			$user = $this->model()->find_by_account($account, true);

		} else {

			$user = $this->get_user();

		}

		$request_key = false;

		if($user) {

			$request_key = base64_encode(json_encode(array(

                        "token" => $user["token"],

                        "request_dt" => $_SERVER["REQUEST_TIME"],

						"request_expire" => $_SERVER["REQUEST_TIME"] + 86400 * 3

			)));

		}

		return $request_key;

	}

	

	public function parse_request_key($request_key) {

		if($request = json_decode(base64_decode($request_key), true)) {

			if(isset($request["request_expire"]) && $request["request_expire"] > $_SERVER["REQUEST_TIME"]) {

				return $request;

			}

		}

		return false;

	}



	private function model() {

		if(empty($this->model)) {

			$this->model = App::model("account");

		}

		return $this->model;

	}

	

	//permission action => user_do : under_user

	public function can_do($permission, $call = null, $failure = null) {

		if(call_user_func_array($this->permission_handler, array($this, $permission))) {

			if(is_callable($call)) {

				call_user_func($call, $this);

			}

		} elseif(isset($failure) && is_callable($failure)) {

			call_user_func($failure, $this);

		}

	}

	

	public function permission_handler($handler) {

		$this->permission_handler = $handler;

	}

	

	public static function default_permission_handler($auth, $permission) {

		$table = array(

			"user" => 10, "manager" => 20, "admin" => 30, "superadmin" => 40, "system" => 50

		);

		$permission = isset($table[$permission]) ? $table[$permission] : 0;

		$user_permission = isset($table[$auth->permission]) ? $table[$auth->permission] : 0;

		return $user_permission >= $permission;

	}

	

	//permission short-cut

	public function system_do($call) {

		$this->can_do("system", $call);

	}

	

	public function admin_do($call) {

		$this->can_do("admin", $call);

	}

	

	public function manager_do($call) {

		$this->can_do("manager", $call);

	}

   
	public function user_do($call) {

		$this->can_do("user", $call);

	}

   
	//under permission short-cut

	public function under_system($call) {

		$this->can_do("system", null, $call);

	}

   
	public function under_admin($call) {

		$this->can_do("admin", null, $call);

	}

	public function under_manager($call) {

		$this->can_do("manager", null, $call);

	}

	public function under_user($call) {

		$this->can_do("user", null, $call);

	}	

	//oauth login : facebook

	public function facebook_login($login) {

		$facebook_api = App::helper("facebook");
        
		if($facebook_profile = $facebook_api->api("/me")) {

			$facebook_id = $facebook_profile["id"];

			call_user_func($login, $facebook_id, $facebook_profile, $this);

		}

	}

}