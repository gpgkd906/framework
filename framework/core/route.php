<?php
if(config::fetch("environment") === "develop") {
	require "error_handler.php";
	error_handler::setup();
}
date_default_timezone_set(config::fetch("timezone") ? : "Asia/Tokyo");
ob_start("ob_gzhandler");
require "App.php";

class route {

  private static $instance;
  private $request;
  public $controller;
  private $start;
  private static $rule = array();
  private static $use_rule = false;
  private $core_dir = null;

  public static function add($path, $action){
	  if(isset(self::$rule[$path])) {
		  trigger_error("ルーティング設定は重複しています", E_USER_WARNING);
	  }
	  self::$use_rule = true;
	  self::$rule[$path] = $action;
  }
  
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

  private function __construct(){
	  $this->core_dir = dirname(dirname(__FILE__));
	  App::setup($this->core_dir);
	  App::route($this);
  }

  public static function getSingletonInstance() {
    if(!isset(self::$instance)) {
      self::$instance = new route();
    }
    return self::$instance;
  }

  /**
   * リクエストをコントローラにマッピングする。
   * etc. admin/login => controller/admin.php :: login()
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
   * コントローラの呼び出し準備及び完了時処理
   * 兼.bootstrap処理
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
   * 400
   */
  public function bad_request($message = "Bad Request") {
	  $this->http(400, $message);
  }
  
  /**
   * 404 not found
   */
  public function not_found($message = "Not Found"){
	  $this->http(404, $message);
  }

  /**
   * 403 forbidden
   */
  public function forbidden($message = "Forbidden"){
	  $this->http(403, $message);
  }
  
  /**
   * 500 internal server error
   */
  public function server_error($message = "Internal Server Error") {
	  $this->http(500, $message);
  }

  /**
   * 301 redirect
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
  
  private function http($code, $message = null) {
	  header("HTTP/1.1 {$code} {$message}");
	  die($message);	  
  }
  
}