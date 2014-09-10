<?php
/**
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
class api extends controller {
	
	//このapiは認証機構を使うか使わないかを設定する
	//RESTFUL APIはstatelessでなければなりませんので、基本的に認証機構は使うべきではありません
	//しかし実際のサービスを構築する時、認証なしではうまくいかないときもあるので、調整可能に。
	public $authorization = false;
	
	//認証機構の個別調整、ここにあるメソッドは認証かけません
	public $out_of_authorization = array(
		"register", 
		"login", 
		"authorized", 
		"facebook_login_url", 
		"reset_password", 
		"reset_password_request"
	);

	protected function before_action(){
		//apiの場合、デバッグ期間でもエラー表示をoffにしておこう
		//表示しても、ブラウザのコンソールから確認しづらいから
		//代わりに、デバッグはapiデータとしてクライアントに返すのがよろしい
		error_handler::off();
		//リリース時はHTTPSを必要とする
		/* if(!isset($_SERVER['HTTPS'])) { */
		/* 	$this->route->forbidden(); */
		/* } */
		
		if($this->authorization) {

			$this->auth = App::helper("auth");

			$this->auth->use_model(App::model("account"));
			
			if(!in_array($this->get_action(), $this->out_of_authorization)) {

				$this->auth->token_authorization($this->param["token"]);

				if(!$this->auth->is_valid()) {

					header("Content-Type: application/json; charset=utf-8");

					die(json_encode(array(

								"status" => false,

								"message" => "権限がありません"

					)));

				}

			}

		}

		$this->set_response_type("json");

	}
	
	protected function after_action(){}
	
	protected function before_render(){}
	
	protected function after_render(){}

	public function none_exist_call() {		

		$status = false;

		$error = true;

		$message = "存在しないapiを呼び出しています、ご確認をお願いします";

		$this->assign(get_defined_vars());

	}
	
	public function post_test() {

		if($this->authorization) {

			$param = $this->param;

			$request = $this->request;

			$this->assign(get_defined_vars());

		} else {

			$this->none_exist_call();

		}

	}

	public function post_authorized() {

		if($this->authorization) {

			$status = false;

			$this->auth->token_authorization($this->param["token"]);

			if($this->auth->is_valid()) {

				$status = true;	

				$profile = App::model("profiles")->find_by_account_id($this->auth->id, true);

			}

			$this->assign(get_defined_vars());

		} else {

			$this->none_exist_call();

		}

	}
	
	public function post_register() {

		if($this->authorization) {

			//有効なパラメタかどか

			$status = false;

			if(isset($this->param["account"]) && isset($this->param["password"])) {

				//重複アカウントかどか

				if(!$target = App::model("account")->find_by_account($this->param["account"])) {

					$id = $this->auth->register($this->param);

					App::model("profiles")->create_record(array("account_id" => $id))->to_array();

					//App::model("alerms")->init($id);

					$status = true;

				} else {

					$message = "メールアドレスが重複してます。";

				}

			}

			$this->assign(get_defined_vars());

		} else {

			$this->none_exist_call();

		}

	}
	
	public function post_login() {

		if($this->authorization) {

			//loginメソッドは内部に、パラメタの有効性をチェックするので、ここではチェックしない

			$this->auth->login($this->param["account"], $this->param["password"]);

			//＊tokenは絶対返さないといけない
			//ログインが失敗する場合、statusがfalseに、token,account_id,user,profileなどはすべてnullになる
			//なので特に対処することはありません
			//null値はどうするかはクライアント側に任せる
			$status = $this->auth->is_valid();
			
			$token = $this->auth->token;

			$account_id = $this->auth->id;

			$user = $this->auth->get_user();

			$profile = App::model("profiles")->find_by_account_id($account_id, true);

			$this->assign(get_defined_vars());

		} else {

			$this->none_exist_call();

		}

	}
	
    public function facebook_login_url() {

		if($this->authorization) {

			$url = App::helper("facebook")->getLoginUrl(array(

					"redirect_uri" => App::helper("view")->get_link("account/api_facebook_login"), 

					"scope" => "email"

			));

			$this->assign(get_defined_vars());

		} else {

			$this->none_exist_call();

		}

	}
	
	public function post_reset_password_request() {

		if($this->authorization) {

			$account = $this->param["account"];

			$status = true;

			if($request_key = App::helper("auth")->generate_request_key($account)) {

				$user = App::model("account")->find_by_account($account);

				App::helper("mail")->send(array(

						"subject" => "パスワードリセット(リクエストキー)",

						"application_name" => config::search("application", "name"),


						"request_key" => $request_key

				), join(PHP_EOL, array(

						"お世話になっております。",

						"いつも「{application_name}」を利用していただきありがとうございます。",

						"",

						"パスワードリセットのリクエストを受付致しましたので、下記のリクエストキーを発行しました。",

						"",

						"{request_key}",

						"",

						"リクエストキーを3日以内にアプリまでコピー&ペーストしていただければ、パスワードの再発行
がご利用になります。",

						"また、リクエストキーを第三者に漏れないよう大切に保管してください。",

						"",

						"今後ともよろしくお願い申し上げます。"

				)), $user->account);

			} else {

				$status = false;

			}

			$this->assign(get_defined_vars());	

		} else {

			$this->none_exist_call();

		}

	}
	
	public function post_reset_password() {

		if($this->authorization) {

			$request_key = $this->param["request_key"];

			$request = App::helper("auth")->parse_request_key($request_key);

			$status = false;

			if($request && $user = App::helper("auth")->get_user_by_request($request)) {

				$status = true;

				//user取得は新しいパスワードを生成する前に取得する必要がある
				//そしてuserが存在する場合だけリクエストを処理する
				//新しいパスワードが生成すると、リクエストが廃棄されるのである

				$new_password = App::helper("auth")->reset_password_by_request($request);

				App::helper("mail")->send(array(

						"subject"=>"パスワードリセット",

						"application_name" => config::search("application", "name"),

						"new_password"=>$new_password

				), join(PHP_EOL, array(

						"お世話になっております。",

						"いつも「{application_name}」を利用していただきありがとうございます。",

						"",

						"パスワードリセットしました。下記は新しいパスワードになります",

						"",

						"{new_password}",

						"",

						"パスワードを第三者に漏れないよう大切に保管してください。",

						"",

						"今後ともよろしくお願い申し上げます。"

				)), $user->account);

			}

			$this->assign(get_defined_vars());

		} else {

			$this->none_exist_call();

		}

	}

}