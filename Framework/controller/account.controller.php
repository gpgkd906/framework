<?php
/**
 * account.controller.php
 *
 * myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework
 *
 * Copyright 2014 Chen Han
 *
 * Licensed under The MIT License
 *
 * @copyright Copyright 2014 Chen Han
 * @link
 * @since
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * account_controller
 * 認証コントローラー
 *
 * @author 2014 Chen Han 
 * @package framework.controller
 * @link 
 */
class account_controller extends application {

	/**
	 * アクション前処理
	 * @api
	 * @return null
	 * @link
	 */
	protected function before_action() {
		if(!App::helper("auth")->is_valid() && !is_callable(array($this, $this->get_action()))) {
			App::route()->redirect("account/login");
		}
		parent::before_action();
	}

	/**
	 * ログアウト処理
	 * @api
	 * @return null
	 * @link
	 */
	public function logout() {
		App::helper("auth")->logout();
		App::route()->redirect("account/login");
	}

	/**
	 * ログイン処理
	 *
	 * ログインフォームを構成する
	 * @api
	 * @return null
	 * @link
	 */
	public function login() {
		$auth = App::helper("auth");
		$auth->use_model(App::model("account"));
		$auth->valid_flag("status", "valid");
		$login = $auth->make_login(function($login) {
				$login->account->class("form-control")->placeholder("account");
				$login->password->class("form-control")->placeholder("password");
				$login->submit->class("btn btn-lg btn-success btn-block")->value("ログイン");
			});
		$auth->login_handler(function($auth) {
				App::route()->redirect("admin");
			}, function($error) {
				$this->set("error", $error);
			});
		$this->assign(get_defined_vars());
	}

	/**
	 * facebook Oauth認証・ログイン処理
	 * @api
	 * @return null
	 * @link
	 */
	public function facebook_login() {
		App::model("account")->facebook_login(function($account, $facebook) {
				App::route()->redirect("index");
			}, function($facebook) {
				App::route()->redirect("account/facebook_register");
			});
	}

	/**
	 * facebook Oauth認証・登録処理
	 * @api
	 * @return null
	 * @link
	 */
	public function facebook_register() {
		$facebook = App::helper("facebook")->api("/me");
		$email = $name = "";
		if(isset($facebook["email"])) {
			$email = $facebook["email"];
		}
		if(isset($facebook["name"])) {
			$name = $facebook["name"];
		}
		$reg = App::model("account")->register($email, $name, function($data, $id) use($facebook) {
				$this->_register_complete($data);
			});
		$this->assign(get_defined_vars());
	}

	/**
	 * restful api用facebook Oauth認証・ログイン処理
	 * @api
	 * @return null
	 * @link
	 */
	public function api_facebook_login() {
		App::model("account")->facebook_login(function($account, $facebook) {
				App::redirect("account/api_facebook_logined?status=authorized&account_id={$account->id}&token={$account->token}");
			}, function($facebook) {
				if(isset($facebook["id"])) {
					$data = array(
						"account" => $facebook["email"],
						"password" => "",
						"facebook_id" => $facebook["id"]
					);
					$id = App::helper("auth")->register($data);
					$facebook_face = "https://graph.facebook.com/" . $facebook["id"] . "/picture";
					App::model("profiles")->create_record(array("account_id" => $id, "name" => $facebook["name"], "nickname" => $facebook["name"], "face" =>"data:image/jpeg;base64," . base64_encode(file_get_contents($facebook_face))));
					$record = App::model("account")->find_by_id($id);
					App::redirect("account/api_facebook_logined?status=authorized&account_id={$id}&token={$record->token}");
				} else {
					App::redirect("account/api_facebook_logined?status=error");
				}


			});
	}
	/**
	 * restful api用facebook Oauth認証・ログイン処理コールバックurl
	 * @api
	 * @return null
	 * @link
	 */
	public function api_facebook_logined() {}

	/**
	 * restful api用twitter Oauth認証・ログイン処理
	 * @api
	 * @return null
	 * @link
	 */
	public function api_twitter_login() {
		App::model("account")->twitter_login(Config::fetch("www") . "account/api_twitter_login", function($account, $twitter) {
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
					$id = App::helper("auth")->register($data);
					App::model("profiles")->create_record(array("account_id" => $id, "name" => $twitter->name, "nickname" => $twitter->screen_name));
					App::model("alerms")->init($id);
					$record = App::model("account")->find_by_id($id);
					App::redirect("account/api_twitter_logined?status=authorized&account_id={$id}&token={$record->token}");
				} else {
					App::redirect("account/api_twitter_logined?status=error");
				}
			});
	}
	/**
	 * restful api用twitter Oauth認証・ログイン処理コールバックurl
	 * @api
	 * @return null
	 * @link
	 */
	public function api_twitter_logined() {}

	/**
	 * twitter Oauth認証・ログイン処理
	 * @api
	 * @return
	 * @link
	 */
	public function twitter_login() {
		App::model("account")->twitter_login(Config::fetch("www") . "account/twitter_login", function($account, $twitter) {
				App::route()->redirect("index");
			}, function($twitter) {
				$reg = App::model("account")->register("", $twitter->name, function($data, $id) use($twitter) {
						//App::model("account_meta")->update_meta($id, "twitter_id", $twitter->id);
						$this->_register_complete($data);
					});
				$this->set("reg", $reg);
				$this->set_template("twitter_register");
			});
		$this->assign(get_defined_vars());
	}

	/**
	 * twitter Oauth認証・登録処理
	 * @api
	 * @return
	 * @link
	 */
	public function twitter_register() {
		App::model("account")->twitter_login(Config::fetch("www") . "account/twitter_login", function() {
				App::route()->redirect("index");
			}, function() {
				App::route()->redirect("account/twitter_register");
			});
	}

	/**
	 * 空のアカウントを生成する(仮登録)
	 * @api
	 * @param   $id
	 * @param    $salt
	 * @return
	 * @link
	 */
	public function add($id = null, $salt = null) {
		if(empty($id) || empty($salt)) {
			App::route()->redirect("account/error");
		}
		$add = App::model("account")->add($id, $salt, function($data, $account) {
				$this->_register_complete($data);
			});
		if(!$add) {
			App::route()->redirect("account/error");
		}
		$this->assign(get_defined_vars());
	}

	/**
	 * アカウント登録完了処理
	 *
	 * システムにログインする
	 *
	 * ユーザー登録メールアドレスに登録完了のお知らせを送る
	 * @api
	 * @param  $data
	 * @return
	 * @link
	 */
	private function _register_complete($data) {
		App::helper("auth")->login($data["mail"], $data["password"]);
		App::helper("mail")->send($data,
			join(PHP_EOL, array()),
			$data["mail"]);
		App::route()->redirect("account/complete");
	}

	/**
	 * アカウントのパスワードをリセットする
	 * @api
	 * @param  $old_token
	 * @return
	 * @link
	 */
	public function reset_password($old_token) {
		App::helper("auth")->reset_password_by_token($old_token);
	}

	/**
	 * 空アクション(view用)
	 * @api
	 * @return
	 * @link
	 */
	public function complete() {
		$this->assign(get_defined_vars());
	}

	/**
	 * 空アクション(view用)
	 * @api
	 * @return
	 * @link
	 */
	public function error() {
		$this->assign(get_defined_vars());
	}



}