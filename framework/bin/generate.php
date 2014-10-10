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
		shell::ls($config["model_dir"], function($filename, $filepath) use($generator){
				global $config;
				list($name, $dummy, $dummy) = explode(".", $filename); 
				$class = $name . "_model";
				require $filepath;
				$methods = array();
				$parent_class = get_parent_class($class);
				foreach(get_class_methods($class) as $tmp) {
					if(strpos($tmp, "__") === 0) {
						continue;
					}
					if(method_exists($parent_class, $tmp)) {
						continue;
					}
					$methods[] = $tmp;
				}
				call_user_func($generator, $name, $methods);
			});
		$generator = generate_test("controller");
		shell::ls($config["controller_dir"], function($filename, $filepath) use($generator){
				global $config;
				@list($name, $type, $dummy) = explode(".", $filename); 
				$class = $name . "_controller";
				if($type !== "controller") {
					return;
				}
				require $filepath;
				$methods = array();
				$parent_class = get_parent_class($class);
				foreach(get_class_methods($class) as $tmp) {
					if(strpos($tmp, "__") === 0) {
						continue;
					}
					if(method_exists($parent_class, $tmp)) {
						continue;
					}
					$methods[] = $tmp;
				}
				call_user_func($generator, $name, $methods);
			});
		break;
}

