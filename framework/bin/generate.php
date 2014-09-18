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
	array("t" => "target", "n" => "name", "p" => "package"),
	array("target" => ["application", "controller", "model", "view", "api", "test"], "package" => ["gpgkd906"])
);

var_dump($arguments);