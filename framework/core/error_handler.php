<?php
/**
 *
 *   Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 *   author: chenhan,gpgkd906@gmail.com
 *   website: http://dev.gpgkd906.com/MyProject/
 */
final class error_handler {
  private static $handler = array();
  private static $logger = null;
  private static $style = "border-color:#000000;border-width:1px;border-style:solid;color:red;padding:10px;margin:10px";
  private static $typeLevel = array(
				  E_ERROR => "重大な実行時エラーが発生しました,プロセスが中断されました。",
				  E_WARNING => "実行時警告(waring)が発生しました。",
				  E_NOTICE => "実行時警告(notice)が発生しました。",
				  E_STRICT => "スクリプト中，将来のPHPバージョンに互換性を持たないしかねないのコードを検出されました。",
				  E_USER_ERROR => "ユーザが定義した実行時エラーが発生しました,プロセスが中断されました。",
				  E_USER_WARNING => "ユーザが定義した実行時警告(waring)が発生しました。",
				  E_USER_NOTICE => "ユーザが定義した実行時警告(notice)が発生しました。",
				  );
  private static $handlerLevel = E_ALL;
  private static $debugMode = true;
  private static $handlerFilter = "self::defaultFilter";
  private static $exceptionHandler = "error_handler::defaultExceptionHandler";
  private static $error_handler = "error_handler::defaultErrorHandler";
  private static $fatalErrorHandler = "error_handler::defaultFatalErrorHandler";

  public static function setHandlerLevel($level){
	  error_reporting($level);
	  self::$handlerLevel = $level;
  }

  public static function setup($level = null) {
	  if(empty($level)) {
		  $level = E_ALL;
	  }
	  self::setHandlerLevel($level);
	  self::setDefaultHandler();
  }
  
  public static function off() {
	  self::$debugMode = false;
  }
  
  public static function on() {
	  self::$debugMode = true;
  }

  public static function write_log($level, $content) {
	  static $log_path;
	  $log_path = dirname(__FILE__) . "/log/error.log";
	  $content = date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]) . PHP_EOL . $content;
	  file_put_contents($log_path, $content . PHP_EOL . PHP_EOL, FILE_APPEND);
	  /* if(isset(self::$logger)){ */
	  /* 	  self::$logger->except($content); */
	  /* 	  self::$logger->write(); */
	  /* }	   */
  }
  
  public static function setHandlerLogger($logger){
    self::$logger = $logger;
  }

  public static function setHandlerFilter($filter){
    self::$handlerFilter = $filter;
  }

  public static function getHandlerLevel(){
    return self::$handlerLevel;
  }

  public static function setErrorHandler($handler){
    self::$error_handler = $handler;
  }
  
  public static function setExceptionHandler($handler){
    self::$exceptionHandler = $handler;
  }

  public static function setDefaultHandler(){
    set_exception_handler("error_handler::proxyExceptionHandler");
    set_error_handler("error_handler::proxyErrorHandler");
    register_shutdown_function("error_handler::proxyFatalErrorHandler");
  }
  
  public static function proxyExceptionHandler($e){
    if(!self::$debugMode || !call_user_func(self::$handlerFilter)){
      return false;
    }
    call_user_func_array(self::$exceptionHandler,array($e));
  }

  public static function defaultExceptionHandler($e){
    $log = array();
    $log[] = "例外が発生しました。";
    $log[] = "File : ".$e->getFile();
    $log[] = "Line : ".$e->getLine();
    $log[] = "Message : ".$e->getMessage();
    $log[] = "Trace:".$e->getTraceAsString();
    $content = join(PHP_EOL, $log);
	self::write_log("except", $content);
    echo nl2br("<div style = '".self::$style."'>".$content."</div>");
    return true;
  }
  
  public static function proxyErrorHandler($errno,$errstr,$errfile,$errline,$errcontext){
    if(!self::$debugMode || !call_user_func(self::$handlerFilter)){
      return false;
    }
    if(!(self::$handlerLevel & $errno)){
      return false;
    }
    call_user_func_array(self::$error_handler,array($errno,$errstr,$errfile,$errline,$errcontext));
  }

  public static function defaultErrorHandler($errno,$errstr,$errfile,$errline,$errcontext){
    $log = array();
    if(empty(self::$typeLevel[$errno])){
      $log[] = "不明なエラーが発生しました";
    }else{
      $log[] = self::$typeLevel[$errno];
    }
    $log[] = "File : ".$errfile;
    $log[] = "Line : ".$errline;
    $log[] = "Message : ".$errstr;
    $content = join(PHP_EOL, $log);
	self::write_log("error", $content);
    echo nl2br("<div style = '".self::$style."'>".$content."</div>");
    return true;
  }
  
  public static function proxyFatalErrorHandler(){
    if( !self::$debugMode || !call_user_func(self::$handlerFilter) ){
      return false;
    }
    call_user_func(self::$fatalErrorHandler);
  }

  public static function defaultFatalErrorHandler(){
    $error = error_get_last();
    if($error === null){
      return false;
    }
    if(( E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT ) & $error["type"]){
      return false;
    }
    $log = array();
    $log[] = "回復不能なエラーが発生しました。";
    $log[] = "File : ".$error["file"];
    $log[] = "Line : ".$error["line"];
    $log[] = "Message : ".$error["message"];
    $content = join(PHP_EOL, $log);
	self::write_log("fatal", $content);
    return true;
  }
  
  private static function defaultFilter(){
	  return true;
  }
  
}