<?php
/**
 * Error_handler.php
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
namespace Framework\Core;
/**
 * Error_handler
 * エラー追跡サブシステム
 *
 * @author 2014 Chen Han
 * @package framework.core
 * @link
 */
final class ErrorHandler {
	/**
	 *各種エラー処理用ハンドラー
	 *
	 * @var array
	 * @link
	 */
	private static $handler = array();
	/**
	 *ロガーインスタンス
	 *
	 * @var NULL
	 * @link
	 */
	private static $logger = null;
	/**
	 *エラー表示用inline-style
	 *
	 * @var String
	 * @link
	 */
	private static $style = "border-color:#000000;border-width:1px;border-style:solid;color:red;padding:10px;margin:10px";
	/**
	 *エラーレベル
	 *
	 * @var array
	 * @link
	 */
	private static $typeLevel = array(
		E_ERROR => "重大な実行時エラーが発生しました,プロセスが中断されました。",
		E_WARNING => "実行時警告(waring)が発生しました。",
		E_NOTICE => "実行時警告(notice)が発生しました。",
		E_STRICT => "スクリプト中，将来のPHPバージョンに互換性を持たないしかねないのコードを検出されました。",
		E_USER_ERROR => "ユーザが定義した実行時エラーが発生しました,プロセスが中断されました。",
		E_USER_WARNING => "ユーザが定義した実行時警告(waring)が発生しました。",
		E_USER_NOTICE => "ユーザが定義した実行時警告(notice)が発生しました。",
	);
	/**
	 * ハンドルするエラーレベル
	 *
	 * @var integer
	 * @link
	 */
	private static $handlerLevel = E_ALL;
	/**
	 * 追跡モード
	 *
	 * @var boolean
	 * @link
	 */
	private static $debugMode = true;
	/**
	 * 例外(Exception)ハンドラー
	 *
	 * @var string
	 * @link
	 */
    const DEFAULT_EXCEPTION_HANDLER = "defaultExceptionHandler";
	private static $exceptionHandler = null;
    
	/**
	 * エラーハンドラー
	 *
	 * @var string
	 * @link
	 */
    const DEFAULT_ERROR_HANDLER = "defaultErrorHandler";
	private static $ErrorHandler = null;
	/**
	 * 致命エラーハンドラー
	 * 
	 * 致命エラーは殆どphpコードの解析段階で起こる
	 *
	 * @var string
	 * @link
	 */
    const DEFAUT_FATAL_ERROR_HANDLER = "defaultFatalErrorHandler";
	private static $fatalErrorHandler = null;

    //
    const DEFAULT_FILTER_HANDLER = "defaultFilter";
    private static $filterHandler = null;

    private static $htmlFormatFlag = true;

	/**
	 * ハンドルするエラーレベルを変更する
	 * @api
	 *
	 * @param  $level
	 * @return
	 * @link
	 */
	public static function setHandlerLevel($level){
		error_reporting($level);
		self::$handlerLevel = $level;
	}

	/**
	 * エラー追跡サブシステムを初期化する
	 * @api
	 *
	 * @param   $level
	 * @return
	 * @link
	 */
	public static function setup($level = null) {
		if(empty($level)) {
			$level = E_ALL;
		}
		self::setHandlerLevel($level);
		self::setDefaultHandler();
	}

	/**
	 * エラー追跡サブシステムをオフにする
	 * @api
	 *
	 * @return
	 * @link
	 */
	public static function off() {
		self::$debugMode = false;
	}

	/**
	 * エラー追跡サブシステムをオンにする
	 * @api
	 *
	 * @return
	 * @link
	 */
	public static function on() {
		self::$debugMode = true;
	}

	/**
	 * ログを記録する
	 * @api
	 *
	 * @param  $level
	 * @param   $content
	 * @return
	 * @link
	 */
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

	/**
	 * ロガーを設定する
	 * @api
	 *
	 * @param  $logger
	 * @return
	 * @link
	 */
	public static function setHandlerLogger($logger){
		self::$logger = $logger;
	}

	/**
	 * ハンドラーフィルターを設定する
	 * @api
	 *
	 * @param  $filter
	 * @return
	 * @link
	 */
	public static function setFilterHandler($filter){
		self::$filterHandler = $filter;
	}

    public static function getFilterHandler()
    {
        if(empty(self::$filterHandler)) {
            return get_class() . "::" . self::DEFAULT_FILTER_HANDLER;
        } else {
            return self::$filterHandler;
        }
    }

	/**
	 * ハンドラーレベルを取得する
	 * @api
	 *
	 * @return
	 * @link
	 */
	public static function getHandlerLevel(){
		return self::$handlerLevel;
	}

	/**
	 * エラーハンドラーを設定する
	 * @api
	 * 
	 * @param  $handler
	 * @return
	 * @link
	 */
	public static function setErrorHandler($handler){
		self::$ErrorHandler = $handler;
	}

	/**
	 * 例外(Exception)ハンドラーを設定する
	 * @api
	 *
	 * @param  $handler
	 * @return
	 * @link
	 */
	public static function setExceptionHandler($handler){
		self::$exceptionHandler = $handler;
	}

	/**
	 * デフォールトハンドラー設定
	 * @api
	 *
	 * @return
	 * @link
	 */
	public static function setDefaultHandler(){
        $className = get_class();
		set_exception_handler($className . "::proxyExceptionHandler");
		set_Error_handler($className . "::proxyErrorHandler");
		register_shutdown_function($className . "::proxyFatalErrorHandler");
	}

	/**
	 * デフォールト例外(Exception)ハンドラー代理
	 * @api
	 *
	 * @param Exception $e 言語Exception
	 * @return
	 * @link
	 */
	public static function proxyExceptionHandler($e){
		if(!self::$debugMode || !call_user_func(self::getFilterHandler())){
			return false;
		}
        if(empty(self::$exceptionHandler)) {
            $exceptionHandler = get_class() . "::" . self::DEFAULT_EXCEPTION_HANDLER;
        } else {
            $exceptionHandler = self::$exceptionHandler;
        }
		call_user_func_array($exceptionHandler,array($e));
	}

	/**
	 * デフォールト例外(Exception)ハンドラー処理
	 * @api
	 *
	 * @param Exception $e 言語Exception
	 * @return
	 * @link
	 */
	public static function defaultExceptionHandler($e){
		$log = array();
		$log[] = "例外が発生しました。";
		$log[] = "File : ".$e->getFile();
		$log[] = "Line : ".$e->getLine();
		$log[] = "Message : ".$e->getMessage();
		$log[] = "Trace:".$e->getTraceAsString();
		$content = join(PHP_EOL, $log);
		self::write_log("except", $content);
        if(self::getHtmlFormatFlag()) {
            echo nl2br("<div style = '".self::$style."'>".$content."</div>");
        } else {
            echo $content;
        }
		return true;
	}

	/**
	 * デフォールトエラーハンドラー代理
	 *@api
	 *
	 * @param  $errno エラーコード
	 * @param  $errstr エラー概要
	 * @param  $errfile エラーが発生したファイル
	 * @param  $errline エラーが発生した行
	 * @param  $errcontext エラー詳細
	 * @return
	 * @link
	 */
	public static function proxyErrorHandler($errno,$errstr,$errfile,$errline,$errcontext){
		if(!self::$debugMode || !call_user_func(self::getFilterHandler())){
			return false;
		}
		if(!(self::$handlerLevel & $errno)){
			return false;
		}
        if(empty(self::$ErrorHandler)) {
            $ErrorHandler = get_class() . "::" . self::DEFAULT_ERROR_HANDLER;
        } else {
            $ErrorHandler = self::$ErrorHandler;
        }
		call_user_func_array($ErrorHandler,array($errno,$errstr,$errfile,$errline,$errcontext));
	}

	/**
	 * デフォールトエラーハンドラー処理
	 *@api
	 *
	 * @param  $errno エラーコード
	 * @param  $errstr エラー概要
	 * @param  $errfile エラーが発生したファイル
	 * @param  $errline エラーが発生した行
	 * @param  $errcontext エラー詳細
	 * @return
	 * @link
	 */
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

	/**
	 * デフォールト致命エラーハンドラー代理
	 *@api
	 *
	 * @return
	 * @link
	 */
	public static function proxyFatalErrorHandler(){
		if( !self::$debugMode || !call_user_func(self::getFilterHandler()) ){
			return false;
		}
        if(empty(self::$fatalErrorHandler)) {
            $fatalErrorHandler = get_class() . "::" . self::DEFAUT_FATAL_ERROR_HANDLER;
        } else {
            $fatalErrorHandler = self::$fatalErrorHandler;
        }
		call_user_func($fatalErrorHandler);
	}

	/**
	 * デフォールト致命エラーハンドラー処理
	 *@api
	 *
	 * @return
	 * @link
	 */
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

	/**
	 * デフォールトハンドラーフィルター
	 *@api
	 *
	 * @return
	 * @link
	 */
	private static function defaultFilter(){
		return true;
	}

    static public function setHtmlFormatFlag($flag)
    {
        self::$htmlFormatFlag = $flag;
    }

    static public function getHtmlFormatFlag()
    {
        return self::$htmlFormatFlag;
    }
}