<?php
/**
 * Controller.php
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
 * Controller
 * フレームワークコントローラースーパークラス
 *
 * @author 2014 Chen Han 
 * @package framework.core
 * @link 
 */
namespace Core;

use Core\App;
use Core\Config;
use Core\Base;

class Controller {
	use Base;
	
/**
 * 使用するヘルパー名のリスト
 * @var array 
 * @link http://
 */
  public $helpers = array("view");

/**
 * 使用するベンダー名のリスト
 *
 * ベンダーとヘルパーは構造上同じだけど、
 *
 * 基本的にヘルパーはフレームワークの拡張と位置づけられる
 *
 * ベンダーはアプリケーションの拡張と位置づけられる。
 *
 * @var array 
 * @link http://
 */
  public $vendors = array();

/**
 * テンプレートエンジンを使用する場合、ここでテンプレートエンジンのインスタンスを格納する、デフォールトは使わない
 * @var object 
 * @link http://
 */
  public $template;

/**
 * リクエスト値を格納する：$_POST, $_GET、$_PUT, $_DELETEなど、実装によるバラツキは激しいかも...
 * @var array 
 * @link http://
 */
  public $request;

/**
 * テンプレートに渡す変数たち
 * @var array 
 * @link http://
 */
  public $tpl_vars = array();

/**
 * 実際生成したモジュールインスタンスを格納するキャッシュ
 * @var array 
 * @link http://
 */
  protected $modules = array();

/**
 * コントローラが内部的に維持する情報
 * @var array 
 * @link http://
 */
  protected $inner_info = array();

/**
 * レスポンスタイプ：html, json, xml
 * @var string 
 * @link http://
 */
  protected $response_type = "html";

  /**
   * rest用パラメタストック
   * @var array
   * @link
   */  
  protected $param = array();

  /**
   * headerを自動設定するかどか
   * @var Boolean
   * @link
   */  
  public static $auto_header = true;

/**
 * 構造器
 * 
 * ヘルパーインスタンス、ベンダーインスタンスを自動生成
 * @param String $name コントローラー名
 * @param String $class コントローラークラス名(継承・パフォーマンス対策)
 * @param Object $route ルーターインスタンス
 * @param Array $request リクエストパラメタ
 * @return
 */
  public function __construct($name, $class, $route, $request) {
	  if(self::$auto_header) {
		  header_remove("Pragma");
		  header_remove("Cache-Control");
	  }
	  session_start();
	  App::controller($this);
	  foreach($this->helpers as $helper) {
		  $this->get_helper($helper);
	  }
	  foreach($this->vendors as $vendor) {
		  $this->get_helper($vendor);
	  }
	  $this->set_class($class);
	  $this->set_name($name);
	  $route->controller = $this;
	  $this->process($route, $request);
  }

/**
 * ヘルパーインスタンス取得
 *
 * ベンダーも同じ方法で取得
 *
 * @param String $helper ヘルパー・ベンダー名
 * @return Object ヘルパー・ベンダーインスタンス名
 */
  public function get_helper($helper) {
      return $this->{$helper} = App::helper($helper);
  }
  
/**
 * モジュールのインスタンス取得
 * @param String $name モジュール名
 * @return
 */
  public function get_module($name) {
	  return App::module($name);
  }
  
/**
 * モジュールを読み込む
 * @param String $module モジュール名
 * @return
 */
  public function import_module($module) {
	  return App::import($module);
  }
  
/**
 * レスポンスタイプを設定する
 * @param String $type html/json/xml
 * @return
 */
  protected function set_response_type($type) {
	  $this->response_type = $type;
  }
  
/**
 * レスポンスタイプを取得する
 * @return
 */
  protected function get_response_type() {
	  return $this->response_type;
  }
  
/**
 * コントローラネームを取得する
 *
 *###://framework/account/loginの場合
 *
 *     echo $controller->get_name(); // "account"
 * 
 *###://framework/product/indexの場合
 *
 *     echo $controller->get_name(); // "product"
 *
 * @return
 */
  public function get_name() {
	  return $this->inner_info["name"];
  }
  
/**
 * コントローラネームを設定する
 * @param string $name
 * @return
 */
  public function set_name($name) {
	  $this->inner_info["name"] = $name; 
	  if(isset($this->view)) {
		  $this->view->set_name($this->inner_info["name"]);
	  }
  }
  
/**
 * 各種類パースを取得
 *
 * e.g.: model_path, helper_path..etc..
 * @param string $cate 種類名
 * @return
 */
  public function get_path($cate) {
	  return App::path($cate);
  }

/**
 * コントローラアクションを取得する
 *
 * ウェブサイトの場合、ひとリクエストに対して、一つの方法が呼ばれる
 *
 * 呼ばれる方法はコントローラアクションとなる
 *
 *###://framework/account/loginの場合
 *
 *     echo $controller->get_action(); // "login"
 * 
 *###://framework/product/indexの場合
 *
 *     echo $controller->get_action(); // "index"
 *
 * @return
 */
  public function get_action() {
	  return $this->inner_info["action"];
  }

/**
 * コントローラアクションを設定する
 * @param string $action 
 * @return
 */
  public function set_action($action) {
	  $this->inner_info["action"] = $action;
  }

/**
 * テンプレート名を取得
 * @return
 */
  public function get_template() {
	  return $this->inner_info["template"];
  }
  
/**
 * テンプレート名を設定する
 * @param string $template テンプレート名
 * @return
 */
  public function set_template($template) {
	  $this->inner_info["template"] = $template;
  }
  
/**
 * リクエストページを取得する
 *###://framework/account/loginの場合
 *
 *     echo $controller->get_action(); // "account/login"
 * 
 *###://framework/product/indexの場合
 *
 *     echo $controller->get_action(); // "product/index"
 *
 * @return
 */
  public function get_page() {
	  return $this->get_name() . "/" . $this->get_action();
  }
    
/**
 * 継承による混乱を避けるため、クラス名を明示的に設定する
 * 
 * @api
 * @param String $class クラス名
 * @return
 * @link
 */
  public function set_class($class) {
	  $this->inner_info["class_name"] = $class;
  }
  
/**
 * クラス名を取得 
 * @api
 * @return String クラス名
 * @link
 */
  public function get_class() {
	  return $this->inner_info["class_name"];
  }
  
/**
 * 設定したテンプレート用パラメタ配列から指定するデータを削除
 * @api
 * @param String $name パラメタ名  
 * @param Array $keys サブパラメタ名
 * @return
 * @link
 */
  protected function remove($name, $keys = array()) {
	  if(isset($this->tpl_vars[$name])) {
		  if(empty($keys)) {
			  unset($this->tpl_vars[$name]);
		  } else {
			  if(is_array($keys)) {
				  foreach($keys as $key) {
					  unset($this->tpl_vars[$name][$key]);					  
				  }
			  } else {
				  unset($this->tpl_vars[$name][$keys]);
			  }
		  }
	  }
  }
  
/**
 * テンプレートに渡す変数を設定する
 * @param String $name 変数名
 * @param Mixed $value 変数値
 * @return
 */
  public function set($name, $value) {
	  $this->tpl_vars[$name] = $value;
  }
  
/**
 * テンプレートに渡した変数を取得する
 * @param String $name 変数名
 * @return
 */
  public function get($name) {
	  return $this->tpl_vars[$name];
  }
  
/**
 * テンプレートに渡す変数を展開して設定
 * @param Array $array テンプレートに渡す変数
 * @return
 */
  public function assign($array) {
	  if(is_array($array)) { 
		  foreach($array as $key => $val) {
			  $this->set($key, $val); 
		  }
	  }
  }
  
/**
 * ルーターに呼ばれる方法、実際の処理方法を定義する
 *
 * @param Object $route ルーターインスタンス
 * @param Array $request ルーターがパース済みのリクエストパラメタ
 * @return
 */
  private function process($route, $request) {
    $this->route = $route;
	$this->request = $request;
    $action = array_shift($this->request);
    if(empty($action)) {
		$action = "index";
    }
	$this->inner_info["rest_action"] = $this->inner_info["action"] = $this->inner_info["template"] = $action;
    if(!empty($this->request)) {
		$request_action = $this->request[count($this->request) - 1];
		if(strpos($request_action, "response_as_") === 0) {
			$response_type = str_replace("response_as_", "", array_pop($this->request));
			$this->set_response_type($response_type);
		}
    }
	$rest_action = $action = $this->inner_info["action"];
	$request_method = isset($_REQUEST["REQUEST_METHOD"]) ? $_REQUEST["REQUEST_METHOD"] : (isset($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "get");
    switch(strtolower($request_method)) {
		case "post":
			$this->param = $_POST;
			$rest_action = "post_" . $action;
			break;
		case "put":
		case "delete":
			parse_str(file_get_contents('php://input'), $request_args);
			$this->param = $request_args;
			$rest_action = $request_method . "_" . $action;
			break;
		case "get":
		default:
			$this->param = $_GET;
		break;
    }
	$this->before_action();
	$this->call_action($action, $rest_action);
    $this->after_action();
    $this->before_render();
    $this->response();
    $this->after_render();
  }
  
/**
 * 実際のアクションを起こす
 * @param string $action urlから呼ばれたaction
 * @param string $rest_action http_method補正後呼ばれたaction
 * @return
 */
  private function call_action($action, $rest_action) {
	  if(method_exists($this, $rest_action)) {		  
		  call_user_func_array(array($this, $rest_action), $this->request);
	  } else if(is_a($this, "api")) {
		  return $this->none_exist_call();
	  }
	  //当是html返回的时候，我们往往需要获得所有在渲染页面时必须的数据，以此兼容普通form操作
	  if($rest_action !== $action && $this->get_response_type() === "html") {
		  if(method_exists($this, $action) && is_callable(array($this, $action))) {
			  call_user_func_array(array($this, $action), $this->request);
			  $called = true;
		  }
	  }
  }

/**
 * レスポンス処理
 * @return
 */
  public function response() {
	  switch(strtolower($this->response_type)) {
		  case "json":
			  $this->response_JSON();
      break;
		  case "xml":
			  $this->response_XML();
			  break;
		  case "html":
		  default:
			  $this->response_html();
		  break;
	  }
  }
  
/**
 * HTMLレスポンス
 * @return
 */
  protected function response_html() {  
	  if(self::$auto_header) {
		  header("Content-Type: text/html; charset=utf-8");
		  header("Expires: " . date($_SERVER["REQUEST_TIME"] + 31530000));
	  }
	  if(App::path("view") !== null) {
		  if($template_engine = Config::fetch("template_engine")) {
			  $template = $this->get_module($template_engine);
			  $template->set_template($this->inner_info["template"]);
			  $template->assign_array($this->tpl_vars);
			  $template->response();
		  } else {
			  $this->view->render($this->tpl_vars, $this->inner_info["template"]);
		  }
		  if($copyright = Config::fetch("copyright")) {
			  echo "<!--" . PHP_EOL;
			  echo $copyright . PHP_EOL;
			  echo "-->";
		  }
	  }
  }
  
/**
 * JSONレスポンス
 * @return
 */
  protected function response_JSON() {
	  if(self::$auto_header) {	  
		  header("Content-Type: application/json; charset=utf-8");
	  }
	  die(json_encode($this->fetch_vars()));    
  }
  
/**
 * XMLレスポンス
 * @return
 */
  protected function response_XML() {
	  $xml = $this->get_module("xml");
	  $root = $this->get_name() . "_" . $this->get_action() . "_Response";
	  $xml->array2xml($root, $this->fetch_vars());
	  if(self::$auto_header) {
		  header("Content-Type: text/xml; charset=utf-8");
	  }
	  die($xml->asXml());
  }

/**
 * テンプレートに渡した変数のなか、json_encodeができない変数を除いて返す
 *
 * @return array
 */
  protected function fetch_vars() {
	  $vars = $this->tpl_vars;
	  foreach($vars as $key => $var) {
		  if(in_array(gettype($var), array("object", "resource"))) {
			  unset($vars[$key]);
		  }
	  }
	  return $vars;
  }
  
}