#!/usr/bin/php
<?php
/**
 *   モデルクラスを自動生成するスクリプト
 *   モデルクラスを自動生成することで
 *   一部よく利用されるデータをクラスメンバーとして処理しておくことができる
 *   よって実行期のパフォーマンスに有利に働く
 *   target: gpgkd906's framework
 *   author: gpgkd906
 *   version: 1.0
 */
echo "THIS IS A SCRIPT FOR AUTO-GENERATE MODEL", PHP_EOL, PHP_EOL;	

//$dir => ~/framework/
require "function.php";
preg_match("/DSN[\"'],\s+?(array[\S\s]+?\))\)/", $config["setup"], $m);
eval("\$DSN={$m[1]};");
if(empty($DSN)){
	echo "no DSN defined", PHP_EOL, "exited!", PHP_EOL;
	die();
}
require $config["core_dir"] . "base.php";
require $config["core_dir"] . "model_driver/{$DSN['type']}.php";
require $config["core_dir"] . "model.php";
$model = new model_core($DSN);

$res = $model->query("show tables")->fetchall_as_array();

$names = array();

foreach($res as $record) {

	$name = array_pop($record);
	
	$names[] = "'$name' => '$name'";
	
	$model_file = $config["model_dir"] . $name . ".model.php";
	
	$model_name = $name . "_model";

	if(is_file($model_file)) {
		regenerate_model($name, $model_name, $model_file, $model);
	} else {
		generate_model($name, $model_name, $model_file, $model);
	}
  
}

//from ver 0.5, application do not define models to use.
//reset_application($names, $application_file);

