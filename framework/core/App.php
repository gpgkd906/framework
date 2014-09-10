<?php
/**
 * Core of framework
 * version: 0.5.0
 * author: gpgkd906@gmail.com / chen han
 */
class App {
	static private $path;
	static private $route;
	static private $controller;
	static private $helpers = array();
	static private $modules = array();

	static public function controller($controller = null) {
		if($controller === null) {
			return self::$controller;
		} else {
			self::$controller = $controller;
		}
	}

	static public function route($route) {
		self::$route = $route;
	}

	static public function redirect($redirect) {
		self::$route->redirect($redirect);
	}

	static public function setup($core_dir) {
	  $path = array(
		  "origin_include_path" => get_include_path(),
		  "core_path" => config::search("path", "core", $core_dir . "/core"),
		  "controller_path" => config::search("path", "controller", $core_dir . "/controller/"),
		  "model_path" => config::search("path", "model", $core_dir . "/model/"),
		  "behavior_path" => config::search("path", "behavior", $core_dir . "/behavior/"),
		  "module_path" => config::search("path", "module", $core_dir . "/module/"),
		  "helper_path" => config::search("path", "helper", $core_dir . "/helper/"),
		  "vendor_path" => config::search("path", "vendor", $core_dir . "/vendor/"),
		  "view_path" => config::search("path", "view", $core_dir . "/view/"),
		  "view_parts_path" => config::search("path", "view_parts", $core_dir . "/view_parts/"),
							);
		self::$path = $path;
		set_include_path(join(PATH_SEPARATOR, $path));
		require "base.php";
		require "controller.php";
		require "application.php";
		require "api.php";
		$dsn_type = config::search("DSN", "type");
		require "model_driver/{$dsn_type}.php";
		require "model.php";
		require "helper.php";
		if(config::search("DSN", "handlersocket")) {
			model_core::use_handlersocket(config::fetch("DSN"));
		}
		if(config::fetch("environment") !== "develop") {
			model_core::track_off();
		}
		model_core::behavior_path($path["behavior_path"]);
	}
	
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
	
	static public function module($name) {
		if(!isset(self::$modules[$name])) {
			if(self::import($name)) {
				self::$modules[$name] = new $name;
			}
		}
		return self::$modules[$name];
	}

	static public function model($name) {
		return model_core::select_model($name, self::$path["model_path"], config::fetch("DSN"));
	}
	
	
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
	
	static public function path($cate) {
		$key = $cate . "_path";
		if(isset(self::$path[$key])) {
			return self::$path[$key];
		}
	}

	/**                                                                                           
	 * マルチプロセス処理                                                                         
	 * 単純処理を分散し高速化する
	 * 別コンテキストに切り替えるので
	 * データベース処理やファイル操作などマルチプロセス対応でない部分は要注意
	 * サブコンテキストで初期化が必要
	 */
	static public function routines($data, $routine, $recur = false, $timeout = 30) {
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

}