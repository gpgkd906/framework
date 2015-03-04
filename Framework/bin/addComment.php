<?php

require "function.php";



//shell::ls($config["controller_dir"], generate_add_comment("controller"));
//shell::ls($config["model_dir"], generate_add_comment("model"));

shell::ls($config["core_dir"], generate_add_comment("core"), array($config["core_dir"]. "/model_driver", $config["core_dir"] . "/model.php", $config["core_dir"] . "/controller.php"));
/*
shell::ls($config["core_dir"], function($name, $file) {
		echo $name, PHP_EOL;
	}, array($config["core_dir"]. "/model_driver", $config["core_dir"] . "/model.php", $config["core_dir"] . "/controller.php"));
*/
