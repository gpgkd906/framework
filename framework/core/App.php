<?php
/**
 * App.php
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

/**
 * App
 * myFrameworkのコアクラス
 *
 * このクラスからフレームワークの全てのリソースをアクセスできる
 * 
 * 旧バージョンとの互換性を考慮して移行中
 *
 * @author 2014 Chen Han
 * @package framework.core
 * @link
 */
class App {
	/**
	 * フレームワーク各リソースのパス情報
	 *
	 * core, model, view(view_parts), controller, helper, vendor, moduleなど
	 *
	 * @var
	 * @link
	 */
	static private $path;
	/**
	 * ルーターインスタンス参照
	 *
	 * @var
	 * @link
	 */
	static private $route;
	/**
	 * カレントコントローラー参照
	 *
	 * @var
	 * @link
	 */
	static private $controller;
	/**
	 * ヘルパー参照プール
	 *
	 * @var array
	 * @link
	 */
	static private $helpers = array();
	/**
	 * モデル参照プール
	 *
	 * @var array
	 * @link
	 */
	static private $modules = array();

	/**
	 * カレントコントローラーの設定または参照
	 * @api
	 * @param Mixed $controller コントローラインスタンス
	 * @return Mixed $controller コントローラインスタンス
	 * @link
	 */
	static public function controller($controller = null) {
		if($controller === null) {
			return self::$controller;
		} else {
			self::$controller = $controller;
		}
	}

	/**
	 * ルーターインスタンス参照の設定 
	 * @api
	 * @param Object $route ルーターインスタンス
	 * @return
	 * @link
	 */
	static public function route($route = null) {
		if($route === null) {
			return self::$route;
		} else {
			self::$route = $route;
		}
	}

	/**
	 * url遷移処理
	 * 内部ルーターインスタンスを利用
	 *
	 *
	 * 旧バージョン：
	 *     
	 *     $route->redirect($action | $url); 
	 *
	 * 推奨：
	 *
	 *     App::redirect($action | $url)
	 *
	 *### $actionの場合
	 *
	 * http://framework/account/profile => http://framework/category/carに遷移を行う場合は
	 *
	 * $actionはcategory/carとなり、サイト内部の遷移ですので、ルーターインスタンスがurlを補完してから遷移する。
	 *
	 *     App::redirect("category/car");
	 *
	 *### $urlの場合
	 *
	 * http://framework/account/profile => http://yahoo.co.jpに遷移を行う場合は
	 *
	 * $urlは外部urlの完全パースとなる、この場合、ルーターインスタンスは$urlに対してそのまま遷移を行う。
	 *
	 *     App::redirect("http://yahoo.co.jp"); 
	 *
	 * @api
	 * @param String $redirect url遷移目標
	 * @return
	 * @link
	 */
	static public function redirect($redirect) {
		self::$route->redirect($redirect);
	}

	/**
	 * フレームワーク初期化
	 *
	 * 各種リソースパスの設定
	 *
	 * 必要最小限のリソース読み込む
	 *
	 * handlersocket利用の判断
	 *
	 * 開発環境の判断
	 * 
	 * プロダクション環境であれば、エラー追跡サブシステムをオフにする
	 *
	 * @api
	 *
	 * @param String $core_dir ~/framework/coreパス
	 * @return
	 * @link
	 */
	static public function setup($core_dir) {
		$path = array(
			"origin_include_path" => get_include_path(),
			"core_path" => Config::search("path", "core", $core_dir . "/core"),
			"controller_path" => Config::search("path", "controller", $core_dir . "/controller/"),
			"model_path" => Config::search("path", "model", $core_dir . "/model/"),
			"behavior_path" => Config::search("path", "behavior", $core_dir . "/behavior/"),
			"module_path" => Config::search("path", "module", $core_dir . "/module/"),
			"helper_path" => Config::search("path", "helper", $core_dir . "/helper/"),
			"vendor_path" => Config::search("path", "vendor", $core_dir . "/vendor/"),
			"view_path" => Config::search("path", "view", $core_dir . "/view/"),
			"view_parts_path" => Config::search("path", "view_parts", $core_dir . "/view_parts/"),
		);
		self::$path = $path;
		set_include_path(join(PATH_SEPARATOR, $path));
		require "Base.php";
		require "Controller.php";
		require "application.php";
		require "api.php";
		$dsn_type = ucfirst(Config::search("DSN", "type"));
		require "model_driver/{$dsn_type}.php";
		require "Model.php";
		require "helper.php";
		if(Config::fetch("environment") !== "develop") {
			Model_core::track_off();
		}
		Model_core::behavior_path($path["behavior_path"]);
	}

	/**
	 * モジュールリソースを読み込む(インスタンスは生成しない)
	 * @api
	 *
	 * @param String $name モジュール名前
	 * @return Boolean 読み込み成功か失敗か
	 * @link
	 */
	static public function import($name) {
		if(!class_exists($name)) {
			$module = $name . ".class.php";
			$module_path = self::$path["module_path"] . $name . "/" . $module;
			if(is_file($module_path)) {
				require $module_path;
			}else {
				return false;
			}
		}
		return true;
	}

	/**
	 * モジュールインスタンスを生成する
	 *
	 * モジュールリソースを判断する，読み込まれてなければ自動的に読み込む
	 *
	 * また、この方法で生成したモジュールは内部的維持されるから、常に同じモジュールインスタンスを参照することが可能
	 * 逆に、同じモジュールの別なるインスタンスを取得する場合は以下の方法で行う
	 * 
	 *     App::import("browser");
	 *     $form1 = new browser;
	 *     $form2 = new browser;
	 *
	 * @api
	 *
	 * @param String $name モジュール名前
	 * @return Object/null モジュールインスタンス
	 * @link
	 */
	static public function module($name) {
		if(!isset(self::$modules[$name])) {
			if(self::import($name)) {
				self::$modules[$name] = new $name;
			}
		}
		return self::$modules[$name];
	}

	/**
	 * ActiveRecord-ORM モデルインスタンスを取得
	 * @api
	 *
	 * @param String $name モデル名(データベース名)
	 * @return
	 * @link
	 */
	static public function model($name) {
		return Model_core::select_model($name, self::$path["model_path"], Config::fetch("DSN"));
	}


	/**
	 * ヘルパーインスタンス・ベンダーインスタンスを取得
	 *
	 * ヘルパーはフレームワークの拡張に位置づけ
	 *
	 * ベンダーはアプリケーションの拡張に位置づけ
	 * @api
	 *
	 * @param String $helper ヘルパー名
	 * @return
	 * @link
	 */
	static public function helper($helper) {
		if(isset(self::$helpers[$helper])) {
			return self::$helpers[$helper];
		}
		$helper_path = self::$path["helper_path"] . $helper . ".helper.php";
		$vendor_path = self::$path["vendor_path"] . $helper . ".vendor.php";
		if(is_file($helper_path)) {
			$helper_name = $helper . "_helper";
			require_once $helper_path;
		} elseif(is_file($vendor_path)) {
			$helper_name = $helper . "_vendor";
			require_once $vendor_path;
		} else {
			return trigger_error("invalid helper or vendor", E_USER_WARNING);
		}
		self::$helpers[$helper] = new $helper_name;
		self::$controller->set($helper_name, self::$helpers[$helper]);
		return self::$helpers[$helper];
	}

	/**
	 * リソースのパスを取得する
	 *
	 * モデルのパスを取得したい場合
	 *
	 *     App::path("model");
	 *
	 * @api
	 *
	 * @param  $cate リソース種類
	 * @return
	 * @link
	 */
	static public function path($cate) {
		$key = $cate . "_path";
		if(isset(self::$path[$key])) {
			return self::$path[$key];
		}
	}

	/**
	 * マルチプロセス処理(pcntlモジュールが必要)
	 *
	 * 単純処理を分散し高速化する
	 *
	 * 別コンテキストに切り替えるので
	 *
	 * データベース処理やファイル操作などマルチプロセス対応でない部分は要注意
	 *
	 * サブコンテキストで初期化が必要
	 *
	 *###複数タスクを並列処理する場合
	 *
	 *     App::routines($tasks, function($task, $pid, $mypid) {
	 *          //ここでデータベースの処理があれば、以下の処理が必要です
	 *          Model_core::connect(Config::fetch("DSN"));
	 *     });
	 *
	 * @api
	 *
	 * @param Array $data 分散処理させたいデータ
	 * @param Closure $routine 分散処理用コールバック
	 * @param integer $timeout 分散処理のタイムオウト時間
	 * @return
	 * @link
	 */
	static public function routines($data, $routine, $timeout = 30) {
		$pids = array();
		foreach($data as $item) {
			$pid = pcntl_fork();
			if($pid == -1) {
				return false;
			} else if($pid) {
				$pids[] = $pid;
				//pcntl_wait($status);
			} else {
				pcntl_alarm($timeout);
				$mypid = getmypid();
				call_user_func($routine, $item, $pid, $mypid);
				die();
			}
		}
		foreach($pids as $pid) {
			pcntl_waitpid($pid, $status);
		}
	}

	/**                                                                                           
	 * マルチ通信、routinesを使うよりも高速なので、api叩くなどネット通信の場合に適切              
	 * 
	 * この並列処理はコンテキストの切り替えがないので、より簡単に扱える
	 *
	 *###複数apiを並列で叩く場合
	 *
	 *     App::wgets($apis, function($api, $raw, $info) {
	 * 
	 *     });
	 *
	 * @api
	 *
	 * @param Array $urls 分散処理させたいurl
	 * @param Closure $routine 分散処理用コールバック
	 * @param integer $timeout 分散処理のタイムオウト時間
	 * @return
	 * @link
	 */
	static public function wgets($urls, $routine, $timeout = 30) {
		$multi_handler = curl_multi_init();
		$chs = array();
		foreach($urls as $url) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_multi_add_handle($multi_handler, $ch);
			$chs[$url] = $ch;
		}
		$running = null;
		do{
			curl_multi_exec($multi_handler, $running);
		} while($running);
		$res = array();
		foreach($chs as $url => $ch) {
			$info = curl_getinfo($ch);
			$raw = curl_multi_getcontent($ch);
			$res[$url] = call_user_func($routine, $url, $raw, $info);
			curl_multi_remove_handle($multi_handler, $ch);
			curl_close($ch);
		}
		curl_multi_close($multi_handler);
		return $res;
    }


}