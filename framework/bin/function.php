<?php
/**
 * config for framework cmd;
 */
if(!isset($config)) {
	$config = array();
	$config["tab"] = "    ";
	$config["dir"] = dirname(dirname(__FILE__));
	$config["core_dir"] = $config["dir"] . "/core/";
	$config["model_dir"] = $config["dir"] . "/model/";
	$config["application_file"] = $config["dir"] . "/controller/application.php";
	$config["entry_dir"] = dirname($config["dir"]);
	$config["setup"] = file_get_contents($config["entry_dir"] . "/index.php");
}

/* 
   function for framework cmd;
*/

function generate_model($from, $default_method = null) {
	global $config;
	$model = model_core::select_model($from);
	$model_name = $from . "_model";
	$model_file = $config["model_dir"] . $from . ".model.php";
	if(is_file($model_file)) {
		return regenerate_model($from);
	}
	$active_name = $from . "_active_record";
	$tab = $config["tab"];
	$columns_define = columns_define($model, $tab);
	$index_define = index_define($model, $tab);
	$class_array = array(
		"<?php",
		"class {$model_name} extends model_core {",
		$columns_define,
		$index_define,
		"public \$relation = array();",
	);
	if(!empty($default_method) && is_array($default_method)) {
		$class_array[] = methods_define($default_method);
	}
	$class_array[] = "";
	$class_array[] = "}";
	$class_array[] = "";
	$class_array[] = "class {$active_name} extends active_record_core {";
	$class_array[] = "";
	$class_array[] = "}";
	$class_define = join(PHP_EOL, $class_array);
	file_put_contents($model_file, $class_define);
	echo "{$from}.model.php was generated", PHP_EOL;
}

function regenerate_model($from) {
	global $config;
	$model = model_core::select_model($from);
	$model_name = $from . "_model";
	$model_file = $config["model_dir"] . $from . ".model.php";
	$exists = file_get_contents($model_file);
	$tab = $config["tab"];
	//columns define
	$reg_columns = "/##columns##[\s\S]*?##columns##/";
	preg_match($reg_columns, $exists, $column_context);
	$columns_define = columns_define($model, $tab);
	$exists = str_replace($column_context[0], $columns_define, $exists);
	//index define
	$reg_index = "/##indexes##[\s\S]*?##indexes##/";
	preg_match($reg_index, $exists, $index_context);
	$index_define = index_define($model, $tab);
	$exists = str_replace($index_context[0], $index_define, $exists);
	//relation define
	/* $reg_relate = "/##relation##[\s\S]*?##relation##/"; */
	/* preg_match($reg_relate, $exists, $relate_context); */
	file_put_contents($model_file, $exists);
	echo "{$from}.model.php was regenerated", PHP_EOL;
}

function methods_define($method_infos) {
	$method_define = array();
	foreach($method_infos as $dm) {
		if(!is_array($dm)) {
			$dm = array(
				"name" => (string) $dm,
				"accessPermission" => "public"
			);
		}
		if(empty($dm["name"])) {
			continue;
		}
		if(empty($dm["accessPermission"])) {
			$dm["accessPermission"] = "public";
		}
		$method_define[] = "";
		$method_define[] = join(PHP_EOL, array("/**",
				"*", 
				"* @param string", 
				"* @param integer", 
				"* @param array",
				"* @param resource",
				"* @param object", 
				"* @param mix", 
				"* @return",
				"*/"));
		$method_define[] = $dm["accessPermission"] . " function " . $dm["name"] . " () {";
		$method_define[] = "}";
	}
	return join(PHP_EOL, $method_define);
}

function columns_define($model, $tab) {
	$fc = alter_column($model);
	$sfc = var_export($fc, true);
	return join(PHP_EOL . $tab, array(
			"##columns##",
			"public \$columns = array(",
			$tab . "'" . join("','", $model->columns(true)) . "'",
			");",
			"public \$alter_columns = " . $sfc . ";", 
			"##columns##"
	));
}

function alter_column($model) {
	$full_columns = $model->full_columns(true);
	$alter = array();
	foreach($full_columns as $col) {
		$raw = array("", "", "", "", "", "");
		$field = null;
		foreach($col as $key => $item) {
			switch($key) {
				case "Field" :
					$field = $item;
					$raw[0] = $model->quote($item);
					break;
				case "Type" :
					$raw[1] = $item;
					break;
				case "Null" :
					$raw[2] = $item === "NO" ? "NOT NULL" : "NULL";
					break;
				case "Key" :
					//pass
					break;
				case "Default":
					$raw[3] = empty($item) ? "" : "Default '{$item}'";
					break;
				case "Extra":
					$raw[4] = strtoupper($item);
					break;
			}
			$alter[$field] = trim(join(" ", $raw));
		}
	}
	return $alter;
}

function index_define($model, $tab) {
	$indexes = $model->indexes(true);
	$sindexes = var_export($indexes, true);
	$fi = alter_index($model);
	$sfi = var_export($fi, true);
	$index_set = array(
		"##indexes##",
		"public \$alter_indexes = " . $sfi . ";"
	);
	$primary = array();
	foreach($indexes as $index) {
		if(strtolower($index["Key_name"]) === "primary") {
			$primary[] = $index['Column_name'];
		}
	}
	$from = $model->get_from();
	$index_set[] = $tab . $tab . "  public \$primary_keys = array('{$from}' => '" . join(",", $primary) . "');";
	$index_set[] = "##indexes##";
	return join(PHP_EOL . $tab, $index_set);
}

function alter_index($model) {
	$indexes = $model->indexes(true);
	$res = $raws = array();
	foreach($indexes as $index) {
		if(!isset($raws[$index["Key_name"]])) {
			$raws[$index["Key_name"]] = array("", "KEY", "", array());
		}
		$target = $raws[$index["Key_name"]];
		foreach($index as $key => $item) {
			switch($key) {
				case "Non_unique" :
					$target[0] = empty($target[0]) ? ($item == 0 ? "UNIQUE" : null) : $target[0];
					break;
				case "Key_name" :
					if(strtolower($item) === "primary") {
						$target[0] = "PRIMARY";
					} else {
						$target[2] = $model->quote($item);
					}
					break;
				case "Column_name" :
					$target[3][] = $model->quote($item);
					break;
			}
		}
		$raws[$index["Key_name"]] = $target;
	}
	foreach($raws as $key => $raw) {
		$raw[3] = "(" . join(",", $raw[3]) . ")";
		$res[$key] = join(" ", $raw);
	}
	return $res;
}

function reset_application($models, $application_file) {
	$app_defined = file_get_contents($application_file);
	preg_match("/public\s*?\\\$models[\s\S]*?\);/", $app_defined, $use_defined);
	$tab = "    ";
	$new_defined = join(PHP_EOL, array(
			'public $models = array(',
			$tab . join(", ", $models),
			$tab . ');'
	));
	$app_defined = str_replace($use_defined, $new_defined, $app_defined);
	//file_put_contents($application_file, $app_defined);
	echo "reset application.php", PHP_EOL;
}

function do_alter_column($model) {
	$alter_column = $model->alter_columns;
	$name = $model->get_table();
	$head = "ALTER TABLE  " . $model->get_from() . " ";
	$fc = alter_column($model);
	#ALTER TABLE `test` DROP `col`;
	$drop = array();
	foreach(array_diff_key($fc, $alter_column) as $key => $alter) {
		$drop[] = "DROP " . $model->quote($key);
	}
	if(!empty($drop)) {
		$drop = $head . join(",", $drop);
		$model->query($drop);
		echo "Columns OF TABLE " . $name . " HAS BE DROPED", PHP_EOL;
	}
	#ALTER TABLE  `test` ADD  `col` INT( 11 ) NOT NULL DEFAULT  '0';
	$add = array();
	foreach(array_diff_key($alter_column, $fc) as $key => $alter) {
		$alter = str_replace("'CURRENT_TIMESTAMP'", 'CURRENT_TIMESTAMP', $alter);
		$alter = str_replace('"CURRENT_TIMESTAMP"', "CURRENT_TIMESTAMP", $alter);
		$add[] = "ADD " . $alter;
	}
	if(!empty($add)) {
		$add = $head . join(",", $add);
		$model->query($add);
		echo "Columns OF TABLE " . $name . " HAS BE ADDED", PHP_EOL;
	}
	#ALTER TABLE  `metas` CHANGE  `meta_key`  `meta_key` INT( 255 ) NULL DEFAULT  '0' COMMENT  'メタキー';
	$change = array();
	foreach($alter_column as $key => $alter) {
		$change[] = "CHANGE " . $model->quote($key) . " " . $alter;
	}
	$change = $head . join(",", $change);
	$model->query($change);
	echo "Columns OF TABLE " . $name . " HAS BE UPDATED", PHP_EOL;
}

function do_alter_index($model) {
	$alter_index = $model->alter_indexes;
	$fi = alter_index($model);
	$head = "ALTER TABLE  " . $model->get_from() . " ";
	$name  =$model->get_table();
	#ALTER TABLE tbl_name DROP INDEX index_name;
	$drop = array();
	foreach(array_diff_key($fi, $alter_index) as $key => $alter) {
		$drop[] = "DROP INDEX " . $model->quote($key);
	}
	if(!empty($drop)) {
		$drop = $head . join(",", $drop);
		$model->query($drop);
		echo "Indexes OF TABLE " . $name . " HAS BE DROPED", PHP_EOL;
	}	
	#ALTER TABLE tbl_name ADD [UNIQUE] {|INDEX|KEY} [index_name] [index_type] (index_col_name,...);
	$add = array();
	foreach(array_diff_key($alter_index, $fi) as $key => $alter) {
		$add[] = "ADD " . $alter;
	}
	if(!empty($add)) {
		$add = $head . join(",", $add);
		$model->query($add);
		echo $add, PHP_EOL;
		echo "Indexes OF TABLE " . $name . " HAS BE ADDED", PHP_EOL;
	}	
	#ALTER TABLE tbl_name DROP INDEX index_name, ADD [UNIQUE] {|INDEX|KEY} [index_name] [index_type] (index_col_name,...)
	$change = array();
	foreach(array_diff($alter_index, $fi) as $key => $alter) {
		$change[] = "DROP INDEX " . $model->quote($key);
		$change[] = "ADD " . $alter;
	}
	
	$change = $head . join(",", $change);
	echo $change, PHP_EOL;
	$model->query($change);
	echo "Indexes OF TABLE " . $name . " HAS BE UPDATED", PHP_EOL;
}

function create_database_table($table) {
	if(empty($table)) {
		return false;
	}
	if(is_array($table)) {
		create_database_table(array_slice($table, 1), $model);
		return create_database_table($table[0], $model);
	}
	//ここにくるのは必ず文字列になる
	$model = model_core::select_model($table);
	$create = array();
	foreach($model->alter_columns as $alter) {
		$alter = str_replace("'CURRENT_TIMESTAMP'", 'CURRENT_TIMESTAMP', $alter);
		$alter = str_replace('"CURRENT_TIMESTAMP"', "CURRENT_TIMESTAMP", $alter);
		$create[] = $alter;
	}
	foreach($model->alter_indexes as $alter) {
		$alter = str_replace("'CURRENT_TIMESTAMP'", 'CURRENT_TIMESTAMP', $alter);
		$alter = str_replace('"CURRENT_TIMESTAMP"', "CURRENT_TIMESTAMP", $alter);
		$create[] = $alter;
	}
	$sql = array("CREATE TABLE IF NOT EXISTS " . $model->quote($table) . " (",
		join("," . PHP_EOL, $create),
		") ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	$model->query(join(PHP_EOL, $sql));
	echo "TABLE {$table} HAS BE CREATED", PHP_EOL;
}

function drop_database_table($table, $confirm = true) {
	if(empty($table)) {
		return false;
	}
	if(is_array($table)) {
		drop_database_table($table[0], $model);
		return drop_database_table(array_slice($table, 1), $model);
	}
	$model = model_core::select_model($table);
	$sql = "DROP TABLE {$table}";
	if($confirm) {
		if(!shell::confirm($sql)) {
			return false;
		}
	}
	$model->query($sql);
	echo "TABLE {$table} HAS BE DROPED", PHP_EOL;
}

