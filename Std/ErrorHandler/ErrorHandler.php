<?php
/**
 * PHP version 7
 * File ErrorHandler.php
 *
 * @category ErrorHandler
 * @package  Framework\ErrorHandler
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\ErrorHandler;

/**
 * Error_handler
 * エラー追跡サブシステム
 *
 * @category ErrorHandler
 * @package  Framework\ErrorHandler
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
*/
final class ErrorHandler
{
    const DEFAULT_EXCEPTION_HANDLER = "defaultExceptionHandler";
    const DEFAULT_ERROR_HANDLER = "defaultErrorHandler";
    const DEFAUT_FATAL_ERROR_HANDLER = "defaultFatalErrorHandler";
    const DEFAULT_FILTER_HANDLER = "defaultFilter";

    private static $_handler = array();
    private static $_logger = null;
    private static $_style = "border-color:#000000;border-width:1px;border-style:solid;color:red;padding:10px;margin:10px";
    private static $_typeLevel = [
        E_ERROR => "重大な実行時エラーが発生しました,プロセスが中断されました。",
        E_WARNING => "実行時警告(waring)が発生しました。",
        E_NOTICE => "実行時警告(notice)が発生しました。",
        E_STRICT => "スクリプト中，将来のPHPバージョンに互換性を持たないしかねないのコードを検出されました。",
        E_USER_ERROR => "ユーザが定義した実行時エラーが発生しました,プロセスが中断されました。",
        E_USER_WARNING => "ユーザが定義した実行時警告(waring)が発生しました。",
        E_USER_NOTICE => "ユーザが定義した実行時警告(notice)が発生しました。",
    ];
    private static $_handlerLevel = E_ALL;
    private static $_debugMode = true;
    private static $_exceptionHandler = null;
    private static $_ErrorHandler = null;
    private static $_fatalErrorHandler = null;
    private static $_filterHandler = null;
    private static $_htmlFormatFlag = true;

    /**
    * ハンドルするエラーレベルを変更する
     *
     * @param string $level errorLevel
     * @return void
     */
    public static function setHandlerLevel($level)
    {
        error_reporting($level);
        self::$_handlerLevel = $level;
    }

    /**
     * エラー追跡サブシステムを初期化する
     *
     * @param string $level errorLevel
     * @return void
     */
    public static function setup($level = null)
    {
        if (empty($level)) {
            $level = E_ALL;
        }
        self::setHandlerLevel($level);
        self::setDefaultHandler();
    }

    /**
     * エラー追跡サブシステムをオフにする
     *
     * @return void
     */
    public static function off()
    {
        self::$_debugMode = false;
    }

    /**
     * エラー追跡サブシステムをオンにする
     *
     * @return void
     */
    public static function on()
    {
        self::$_debugMode = true;
    }

    /**
     * ログを記録する
     *
     * @param string $level   errorLevel
     * @param string $content errorMessage
     * @return void
     */
    public static function writeLog($level, $content)
    {
        static $log_path;
        $log_path = dirname(__FILE__) . "/log/error.log";
        $content = date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]) . PHP_EOL . $content;
        file_put_contents($log_path, $content . PHP_EOL . PHP_EOL, FILE_APPEND);
    }

    /**
     * ロガーを設定する
     *
     * @param Logger $logger Logger
     *
     * @return void
     */
    public static function setHandlerLogger($logger)
    {
        self::$_logger = $logger;
    }

    /**
     * ハンドラーフィルターを設定する
     *
     * @param callback $filter ErrorHandlerFilter
     *
     * @return void
     */
    public static function setFilterHandler($filter)
    {
        self::$_filterHandler = $filter;
    }

    /**
     * ハンドラーフィルターを取得する
     *
     * @return callback $filter
     */
    public static function getFilterHandler()
    {
        if (empty(self::$_filterHandler)) {
            return self::class . "::" . self::DEFAULT_FILTER_HANDLER;
        } else {
            return self::$_filterHandler;
        }
    }

    /**
     * ハンドラーレベルを取得する
     *
     * @return integer $errorLevel
     */
    public static function getHandlerLevel()
    {
        return self::$_handlerLevel;
    }

    /**
     * エラーハンドラーを設定する
     *
     * @param callback $handler errorHanlder
     *
     * @return void
     */
    public static function setErrorHandler($handler)
    {
        self::$_ErrorHandler = $handler;
    }

    /**
     * 例外(Exception)ハンドラーを設定する
     *
     * @param callback $handler ExceptionHandler
     *
     * @return void
     */
    public static function setExceptionHandler($handler)
    {
        self::$_exceptionHandler = $handler;
    }

    /**
     * デフォールトハンドラー設定
     *
     * @return void
     */
    public static function setDefaultHandler()
    {
        $className = self::class;
        set_exception_handler($className . "::proxyExceptionHandler");
        set_Error_handler($className . "::proxyErrorHandler");
        register_shutdown_function($className . "::proxyFatalErrorHandler");
    }

    /**
     * デフォールト例外(Exception)ハンドラー代理
     *
     * @param Exception $e 言語Exception
     *
     * @return void
     */
    public static function proxyExceptionHandler($e)
    {
        if (!self::$_debugMode || !call_user_func(self::getFilterHandler())) {
            return false;
        }
        if (empty(self::$_exceptionHandler)) {
            $exceptionHandler = self::class . "::" . self::DEFAULT_EXCEPTION_HANDLER;
        } else {
            $exceptionHandler = self::$_exceptionHandler;
        }
        call_user_func_array($exceptionHandler, [$e]);
    }

    /**
     * デフォールト例外(Exception)ハンドラー処理
     *
     * @param Exception $e 言語Exception
     *
     * @return void
     */
    public static function defaultExceptionHandler($e)
    {
        $log = array();
        $log[] = "例外が発生しました。";
        $log[] = "File : ".$e->getFile();
        $log[] = "Line : ".$e->getLine();
        $log[] = "Message : ".$e->getMessage();
        $log[] = "Trace:".$e->getTraceAsString();
        $content = join(PHP_EOL, $log);
        self::writeLog("except", $content);
        if (self::getHtmlFormatFlag()) {
            echo nl2br("<div style = '".self::$_style."'>".$content."</div>");
        } else {
            echo $content;
        }
        return true;
    }

    /**
     * デフォールトエラーハンドラー代理
     *
     * @param string  $errno      エラーコード
     * @param string  $errstr     エラー概要
     * @param string  $errfile    エラーが発生したファイル
     * @param integer $errline    エラーが発生した行
     * @param string  $errcontext エラー詳細
     *
     * @return void
     */
    public static function proxyErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        if (!self::$_debugMode || !call_user_func(self::getFilterHandler())) {
            return false;
        }
        if (!(self::$_handlerLevel & $errno)) {
            return false;
        }
        if (empty(self::$_ErrorHandler)) {
            $ErrorHandler = self::class . "::" . self::DEFAULT_ERROR_HANDLER;
        } else {
            $ErrorHandler = self::$_ErrorHandler;
        }
        call_user_func_array($ErrorHandler, [$errno, $errstr, $errfile, $errline, $errcontext]);
    }

    /**
     * デフォールトエラーハンドラー処理
     *
     * @param string  $errno      エラーコード
     * @param string  $errstr     エラー概要
     * @param string  $errfile    エラーが発生したファイル
     * @param integer $errline    エラーが発生した行
     * @param string  $errcontext エラー詳細
     *
     * @return void
     */
    public static function defaultErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $log = array();
        if (empty(self::$_typeLevel[$errno])) {
            $log[] = "不明なエラーが発生しました";
        } else {
            $log[] = self::$_typeLevel[$errno];
        }
        $log[] = "File : " . $errfile;
        $log[] = "Line : " . $errline;
        $log[] = "Message : " . $errstr;
        $content = join(PHP_EOL, $log);
        self::writeLog("error", $content);
        echo nl2br("<div style = '" . self::$_style . "'>" . $content . "</div>");
        return true;
    }

    /**
     * デフォールト致命エラーハンドラー代理
     *
     * @return void
     */
    public static function proxyFatalErrorHandler()
    {
        if (!self::$_debugMode || !call_user_func(self::getFilterHandler())) {
            return false;
        }
        if (empty(self::$_fatalErrorHandler)) {
            $fatalErrorHandler = self::class . "::" . self::DEFAUT_FATAL_ERROR_HANDLER;
        } else {
            $fatalErrorHandler = self::$_fatalErrorHandler;
        }
        call_user_func($fatalErrorHandler);
    }

    /**
     * デフォールト致命エラーハンドラー処理
     *
     * @return void
     */
    public static function defaultFatalErrorHandler()
    {
        $error = error_get_last();
        if ($error === null) {
            return false;
        }
        if (( E_WARNING | E_NOTICE | E_USER_WARNING | E_USER_NOTICE | E_STRICT ) & $error["type"]) {
            return false;
        }
        $log = array();
        $log[] = "回復不能なエラーが発生しました。";
        $log[] = "File : ".$error["file"];
        $log[] = "Line : ".$error["line"];
        $log[] = "Message : ".$error["message"];
        $content = join(PHP_EOL, $log);
        self::writeLog("fatal", $content);
        return true;
    }

    /**
     * デフォールトハンドラーフィルター
     *
     * @return void
     */
    private static function defaultFilter()
    {
        return true;
    }

    /**
     * Error表示をHTML化するかどか
     *
     * @param bool $flag HtmlFormatFlag
     *
     * @return void
     */
    public static function setHtmlFormatFlag($flag)
    {
        self::$_htmlFormatFlag = $flag;
    }

    /**
     * HTML化Error表示するかどか取得
     *
     * @return bool
     */
    public static function getHtmlFormatFlag()
    {
        return self::$_htmlFormatFlag;
    }
}
