#!/usr/bin/php
<?php
/**
 *   自動生成スクリプト
 *   @target: gpgkd906's framework
 *   @author: gpgkd906
 *   @version: 1.0
 */
require "function.php";

$arguments = shell::get_args(
	array("target" => "controller", "name" => null, "package" => "gpgkd906"),
	array(0=> "target", "t" => "target", "n" => "name", "p" => "package"),
	array("target" => ["application", "controller", "model", "view", "api", "test"], "package" => ["gpgkd906"])
);


switch($arguments["target"]) {
	case "application": 

		break;
	case "controller":

		break;
	case "model": 

		break;
	case "view":

		break;
	case "api":

		break;
	case "test":
		if(!is_dir($config["test_dir"])) {
			mkdir($config["test_dir"]);
			mkdir($config["test_dir"] . "/model");
			mkdir($config["test_dir"] . "/controller");
		}
		//model
		$generator = generate_test("model");
		$model_tests = array();
		shell::ls($config["model_dir"], function($filename, $filepath) use($generator){
				global $config, $model_tests;
				list($name, $dummy, $dummy) = explode(".", $filename); 
				$class = $name . "_model";
				require $filepath;
				$model = new $class($config["DSN"]);
				$methods = array();
				foreach(get_class_methods($model) as $tmp) {
					if(strpos($tmp, "__") === 0) {
						continue;
					}
					if(method_exists("model_core", $tmp)) {
						continue;
					}
					$methods[] = $tmp;
				}
				$model_tests[$name . "Test"] = call_user_func($generator, $name, $methods);
			});
		print_r($model_tests);
		break;
}

