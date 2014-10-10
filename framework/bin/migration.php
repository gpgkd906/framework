#!/usr/bin/php
<?php
/**
 *   データベースあるいはを自動生成・変更するスクリプト
 *   モデルクラスで定義した情報を利用して
 *   データベースを生成あるいは変更することができる。
 *   @api $console: php migration [-s/-source=input/db/database/model/class] [-m/-mode=confirm/silent]
 *   @target gpgkd906's framework
 *   @author gpgkd906
 *   @version 1.0
 */
require "function.php";

$model = Model_core::select_model(null, $config["model_dir"], $config["DSN"]);

$tables = array();
$res = $model->query("show tables")->fetchall_as_array();
foreach($res as $item) {
	$tables[] = array_pop($item);
}

$config["argv"] = shell::get_args(
	array("source" => "input", "mode" => "confirm"), 
	array("s" => "source", "m" => "mode"),
	array("source" => array("input", "db/database", "class/model"), "mode" => array("confirm", "silent/skip"))
);

switch($config["argv"]["source"]) {
	case "db":
	case "database":
		echo "GENERATE MODEL FROM DATABASE", PHP_EOL, PHP_EOL;
	    shell::copy_dir($config["dir"] . "/model", $config["dir"] . "/bin/migration/model_" . date("Ymd_His", $_SERVER["REQUEST_TIME"]));
		foreach($res as $record) {
			$name = array_pop($record);
			generate_model($name);
		}
		break;
	case "class":
	case "model":
		echo "GENERATE DATABASE FROM MODEL", PHP_EOL, PHP_EOL;	
	    shell::ls($config["model_dir"], function($filename, $filepath) {
				global $config, $tables;
				list($name, $dummy, $dummy) = explode(".", $filename); 
				$target = Model_core::select_model($name, $config["model_dir"]);
				if(in_array($name, $tables)) {
					$tables = array_diff($tables, array($name));
					$fc = alter_column($target);
					$fi = alter_index($target);
					if($fc !== $target->alter_columns) {
						do_alter_column($target);
					}
					if($fi !== $target->alter_indexes) {
						do_alter_index($target);
					}
				} else {
					create_database_table($name);
				}
			});
		drop_database_table(array_values($tables), ($config["argv"]["mode"] !== "silent"));
	break;
	case "input":
	default:
		echo "GENERATE DATABASE AND MODEL", PHP_EOL, PHP_EOL;
	    $migration_do_backup = true;
	    while(shell::confirm("create new model")) {
			$tbl_name = shell::read("input table name: ", false);
			if(in_array($tbl_name, $tables)) {
				echo "table {$tbl_name} was existed!";
				continue;
			}
			$target = Model_core::select_model($tbl_name);
			$target->alter_columns = array();
			$target->alter_indexes = array();
			$target->primary_keys = array();
			#confirm primary key
			if(shell::confirm("auto generate primary key")) {
				$primary_name = "id";
				$primary_type = "int(11)";
				$primary_ai = "AUTO_INCREMENT";
			} else {
				$primary_name = shell::read("input primary key name : ", false);
				$primary_type = shell::read("input primary key type [etc. int(11)] : ", false);
				$primary_ai = shell::confirm("is primary key auto increment") ? "AUTO_INCREMENT" : "";
			}
			$target->alter_columns[$primary_name] = join(" ", array($target->quote($primary_name), $primary_type, "NOT NULL", $primary_ai));
			$target->alter_indexes["PRIMARY"] = "PRIMARY KEY (" . $target->quote($primary_name) . ")";
			$target->primary_keys[$target->quote($tbl_name)] = $primary_name;
			#add columns
			while(shell::confirm("continue add column")) {
				$col_name = shell::read("input column name : ", false);
				$col_type = shell::read("input column type [etc. int(11)] : ", false);
				$col_null = shell::confirm("could column be null") ? "NULL" : "NOT NULL";
				$col_default = ($default = shell::read("input column default : ")) ? "Default '{$default}'" : "";
				$target->alter_columns[$col_name] = join(" ", array($target->quote($col_name), $col_type, $col_null, $col_default));
			}
			#confirm register_datetime/update_datetime => timestamp
			if(shell::confirm("add timestamp column to track record")) {
				$target->alter_columns["register_dt"] = '`register_dt` bigint(20) NOT NULL';
				$target->alter_columns["update_dt"] = '`update_dt` bigint(20) NOT NULL';
			}
			#add indexes
			while(shell::confirm("continue add indexes")) {
				$idx_name = shell::read("input index/key name: ", false);
				$idx_type = shell::read_select("select index/key type: ", array("INDEX", "UNIQUE", "SPATIAL", "FULLTEXT"));
				$idx_content = shell::read("input index/key columns: ", false);
				$target->alter_indexes[$idx_name] = join(" ", array($idx_type, $target->quote($idx_name), "(", str_replace(array(",", "、", "，"), "`,`", $target->quote($idx_content)), ")"));
			}
			#create table
			create_database_table($tbl_name);
			#add default_method
			$default_method = array();
			while(shell::confirm("continue add default method")) {
				$method_name = shell::read("input method name: ", false);
				$method_access = shell::read_select("select method [{$method_name}] access permission : ", array("public", "protected", "private"));
				$default_method[$method_name] = array("name" => $method_name, "accessPermission" => $method_access);
			}
			#create model
			if($migration_do_backup) {
				$migration_do_backup = false;
				shell::copy_dir($config["dir"] . "/model", $config["dir"] . "/bin/migration/model_" . date("Ymd_His", $_SERVER["REQUEST_TIME"]));
			}
			generate_model($tbl_name, $default_method);
		}
		break;
}