<?php
/**
 * route.php
 *
 * myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework
 * Copyright 2014 Chen Han
 *
 * Licensed under The MIT License
 *
 * @copyright Copyright 2014 Chen Han
 * @link
 * @since
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

if(config::fetch("environment") === "develop") {
	require "error_handler.php";
	error_handler::setup();
}
date_default_timezone_set(config::fetch("timezone") ? : "Asia/Tokyo");
ob_start("ob_gzhandler");
require "App.php";

/**
 * route
 * ルータークラス
 *
 * http リクエストをフレームワークが処理しやすい用に解析する
 *
 * 主にurl解析を行う
 *
 * @author 2014 Chen Han
 * @package framework.core
 * @link
 */
class route {

	/**
	 * ルータークラスのシングルスタンス
	 *
	 * @var
	 * @link
	 */
	private static $instance;
	/**
	 * リクエストパラメタ
	 *
	 * @var
	 * @link
	 */
	private $request;
	/**
	 * コントローラーインスタンス
	 *
	 * @var
	 * @link
	 */
	public $controller;
	/**
	 * urlパース用正規表現ルール
	 *
	 * パフォーマンスの考慮で現在は使っていません
	 * @var array
	 * @link
	 */
	private static $rule = array();
	/**
	 * 正規表現でurlパースするかしないか
	 *
	 * @var boolean
	 * @link
	 */
	private static $use_rule = false;
	/**
	 * ~/framework/coreのパス
	 *
	 * @var NULL
	 * @link
	 */
	private $core_dir = null;

	/**
	 * 正規表現ルール追加
	 * @api
	 *
	 * @param  $path
	 * @param   $action
	 * @return
	 * @link
	 */
	public static function add($path, $action){
		if(isset(self::$rule[$path])) {
			trigger_error("ルーティング設定は重複しています", E_USER_WARNING);
		}
		self::$use_rule = true;
		self::$rule[$path] = $action;
	}

	/**
	 * 正規表現によるurlパース
	 * @api
	 *
	 * @param  $path
	 * @return
	 * @link
	 */
	private function regular_parse($path) {
		if(!self::$use_rule) {
			return explode("/", $path);
		}
		$rule = self::$rule;
		if(isset($rule[$path])) {
			$req = $rule[$path];
		} else {
			foreach($rule as $key => $val) {
				$key = str_replace("/", "\/", $key);
				if(preg_match("/" . $key . "/", $path)) {
					$req = $val;
					break;
				}
			}
		}
		return explode("/", $req);
	}

	/**
	 * 外部からのインスタンス化を阻止
	 * @api
	 *
	 * @return
	 * @link
	 */
	private function __construct(){
		$this->core_dir = dirname(dirname(__FILE__));
		App::setup($this->core_dir);
		App::route($this);
	}

	/**
	 * routeのシングルインスタンスを取得する
	 * @api
	 *
	 * @return
	 * @link
	 */
	public static function getSingletonInstance() {
		if(!isset(self::$instance)) {
			self::$instance = new route();
		}
		return self::$instance;
	}

	/**
	 * リクエストをコントローラにマッピングする。
	 * 
	 * リクエストurlをパースする
	 *
	 * @api
	 *
	 * @param  $req
	 * @return
	 * @link
	 */
	public function mapping($req) {
		//.はクラス名としても、メソッド名としても使えないから
		if(strpos($req, ".")) {
			$this->not_found();
		}
		//最後の一文字が'/'であれば、削除する
		if(substr($req, -1, 1) === "/"){
			$req = substr($req, 0, -1);
		}
		//$req = self::regular_parse($req);
		$req = explode("/", $req);
		$_req = $req[0];
		if(!isset($_req[0])){
			$req = array("index");
		}
		$this->request = $req;
	}


	/**
	 * パースしたリクエストに応じて適切にコントローラーを生成する
	 *
	 * そして、リクエストデータをコントローラーに渡して、ルーター処理を終了する
	 *
	 * @api
	 *
	 * @return
	 * @link
	 */
	public function request() {
		$page = array_shift($this->request);
		$controller_path = $this->core_dir . "/controller/" . $page . ".controller.php";
		if(is_file($controller_path)) {
			require $controller_path;
			$controller_name = $page . "_controller";
			$this->controller = new $controller_name($page, $page, $this, $this->request);
		} else {
			$this->controller = new application($page, "application", $this, $this->request);
		}
	}

	/**
	 * 400 Bad Request
	 * @api
	 *
	 * @param string $message
	 * @return
	 * @link
	 */
	public function bad_request($message = "Bad Request") {
		$this->http(400, $message);
	}

	/**
	 * 404 Not Found
	 * @api
	 *
	 * @param string $message
	 * @return
	 * @link
	 */
	public function not_found($message = "Not Found"){
		$this->http(404, $message);
	}

	/**
	 * 403 Forbidden
	 * @api
	 *
	 * @param string $message
	 * @return
	 * @link
	 */
	public function forbidden($message = "Forbidden"){
		$this->http(403, $message);
	}

	/**
	 * 500 Internal Server Error
	 * @api
	 *
	 * @param string $message
	 * @return
	 * @link
	 */
	public function server_error($message = "Internal Server Error") {
		$this->http(500, $message);
	}

	/**
	 * 301によるurl遷移
	 *
	 * @api
	 *
	 * @param string $to
	 * @return
	 * @link
	 */
	public function redirect($to = ""){
		if($this->controller->get_page() === $to) {
			return false;
		}
		$is_url = false;
		if( strpos($to, "http://") === 0 || strpos($to, "https://") === 0 ){
			$is_url=true;
		}
		if(!$is_url){
			$to = config::fetch("www") . $to;
		}
		header('Location:' . $to, true, 301);
		die();
	}

	/**
	 * http codeを出力する
	 * @api
	 *
	 * @param  $code http code
	 * @param    $message ユーザーに返事するメッセージ
	 * @return
	 * @link
	 */
	private function http($code, $message = null) {
		header("HTTP/1.1 {$code} {$message}");
		die($message);
	}

}