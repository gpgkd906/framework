<?php
if(php_sapi_name() !== "cli") {
	trigger_error("この処理はコンソール専用です", E_USER_ERROR);
	die();
}
/**
 * config for framework cmd;
 */
if(!isset($config)) {
	$config = array();
}
$config["tab"] = "    ";
$config["dir"] = dirname(dirname(__FILE__));
$config["core_dir"] = $config["dir"] . "/core";
$config["model_dir"] = $config["dir"] . "/model";
$config["controller_dir"] = $config["dir"] . "/controller";
$config["helper_dir"] = $config["dir"] . "/helper";
$config["vendor_dir"] = $config["dir"] . "/vendor";
$config["view_dir"] = $config["dir"] . "/view";
$config["test_dir"] = $config["dir"] . "/test";
$config["view_parts_dir"] = $config["dir"] . "/view_parts";
$config["module_dir"] = $config["dir"] . "/module";
$config["bin_dir"] = $config["dir"] . "/bin";
$config["application_file"] = $config["dir"] . "/controller/application.php";
$config["entry_dir"] = dirname($config["dir"]);
preg_match("/DSN[\"'],\s+?(array[\S\s]+?\))\)/", file_get_contents($config["entry_dir"] . "/index.php"), $m);
//$config["setup"] = file_get_contents($config["entry_dir"] . "/index.php");
//preg_match("/DSN[\"'],\s+?(array[\S\s]+?\))\)/", $config["setup"], $m);
eval("\$DSN={$m[1]};");
if(empty($DSN)){
	echo "no DSN defined", PHP_EOL, "exited!", PHP_EOL;
	die();
}
$config["DSN"] = $DSN;
require $config["core_dir"] . "/base.php";
require $config["core_dir"] . "/model_driver/{$DSN['type']}.php";
require $config["core_dir"] . "/model.php";
require $config["dir"] . "/module/shell/shell.class.php";

/** 
 * define function for framework console cmd;
 */

function generate_model($from, $default_method = null) {
	global $config;
	$model = model_core::select_model($from);
	$model_name = $from . "_model";
	$model_file = $config["model_dir"] . "/" . $from . ".model.php";
	if(is_file($model_file)) {
		return regenerate_model($from);
	}
	$active_name = $from . "_active_record";
	$tab = $config["tab"];
	$columns_define = columns_define($model, $tab);
	$index_define = index_define($model, $tab);
	$class_array = array(
		"<?php",
		"/**",
		"* {$from}.model.php",
		"*",
		"*",
		"* myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework",
		"* Copyright " . date("Y") . " Chen Han",
		"*",
		"* Licensed under The MIT License",
		"*",
		"* @copyright Copyright " . date("Y") . " Chen Han",
		"* @link",
		"* @since",
		"* @license http://www.opensource.org/licenses/mit-license.php MIT License",
		"*/",
		"/**",
		"* {$model_name}",
		"*",
		"* ",
		"*",
		"* @author " . date("Y") . " Chen Han ",
		"* @package framework.model",
		"* @link ",
		"*/",
		"class {$model_name} extends model_core {",
		$columns_define,
		$index_define,
		"/**",
		"* 対応するActiveRecordクラス名",
		"* @api",
		"* @var String",
		"* @link",
		"*/",
		"public \$active_record_name = '{$active_name}';",
		"/**",
		"* 結合情報",
		"* @api",
		"* @var Array",
		"* @link",
		"*/",
		"public \$relation = array();",
	);
	if(!empty($default_method) && is_array($default_method)) {
		$class_array[] = methods_define($default_method);
	}
	$class_array[] = "";
	$class_array[] = "}";
	$class_array[] = "";
	$class_array[] = "/**";
	$class_array[] = " * {$active_name}";
	$class_array[] = " * ";
	$class_array[] = " * ";
	$class_array[] = " *";
	$class_array[] = " * @author " . date("Y") . " Chen Han ";
	$class_array[] = " * @package framework.model";
	$class_array[] = " * @link ";
	$class_array[] = " */";
	$class_array[] = "class {$active_name} extends active_record_core {";
	$class_array[] = active_define($from, $model);
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
	$model_file = $config["model_dir"] . "/" . $from . ".model.php";
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
	//active define
	$active_define = active_define($from, $model);
	$reg_active = "/###active_define###[\s\S]*?###active_define###/";
	preg_match($reg_active, $exists, $active_context);
	$exists = str_replace($active_context[0], $active_define, $exists);
	file_put_contents($model_file, $exists);
	echo "{$from}.model.php was regenerated", PHP_EOL;
}

function active_define($from, $model) {
	$active_name = $from . "_active_record";
	$store_schema = array_flip($model->columns(true));
	$indexes = $model->indexes(true);
	$primary = array();
	foreach($indexes as $index) {
		if(strtolower($index["Key_name"]) === "primary") {
			$primary[] = $index['Column_name'];
		}
	}
	$primary_key = join(",", $primary);
	$active_array = array(
		"###active_define###",
		"/**",
		"*",
		"* テーブル名",
		"* @api",
		"* @var ",
		"* @link",
		"*/",
		"protected static \$from = '{$from}';",
		"/**",
		"*",
		"* プライマリキー",
		"* @api",
		"* @var ",
		"* @link",
		"*/",
		"protected static \$primary_key = '{$primary_key}';",
		"/**",
		"* モデルのカラムの反転配列。",
		"* ",
		"* 反転後issetが働ける、パフォーマンス的にいい",
		"*",
		"* 反転は自動生成するので，実行時に影響はありません",
		"* @api",
		"* @var ",
		"* @link",
		"*/",
		"protected static \$store_schema = " . var_export($store_schema, true) . ";",
		"/**",
		"* 遅延静的束縛：現在のActiveRecordのカラムにあるかどか",
		"* @api",
		"* @param String \$col チェックするカラム名",
		"* @return",
		"* @link",
		"*/",
		"public static function has_column(\$col) {",
		"	return isset(self::\$store_schema[\$col]);",
		"}",
		"/**",
		"* 遅延静的束縛：ActiveRecordのテーブル名を取得",
		"* @api",
		"* @return",
		"* @link",
		"*/",
		"public static function get_from() {",
		"	return self::\$from;",
		"}",
		"/**",
		"* 遅延静的束縛：ActiveRecordのプライマリーキーを取得",
		"* @api",
		"* @return",
		"* @link",
		"*/",
		"public static function get_primary_key() {",
		"	return self::\$primary_key;",
		"}",
		"###active_define###");
	return join(PHP_EOL, $active_array);
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
				"* @api",
				"* @param string", 
				"* @param integer", 
				"* @param array",
				"* @example ",
				"* @author Chen Han <gpgkd906@gmail.com>",
				"* @copyright 2010-" . date("Y") . " Chen Han",
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
			"/**",
			"* カラム",
			"* @api",
			"* @var array",
			"* @link",
			"*/",
			"public \$columns = array(",
			$tab . "'" . join("','", $model->columns(true)) . "'",
			");",
			"/**",
			"* カラム定義",
			"* @api",
			"* @var array",
			"* @link",
			"*/",
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
		"/**",
		"* インデックス定義",
		"* @api",
		"* @var array",
		"* @link",
		"*/",
		"public \$alter_indexes = " . $sfi . ";",
		"/**",
		"* プライマリーキー",
		"* @api",
		"* @var array",
		"* @link",
		"*/",
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

function generate_add_comment($package) {
	//カリー関数
	return function($fname, $file) use ($package) {
		$content = file_get_contents($file);
		//クラス定義
		$content = preg_replace_callback("/(?:final\s+|abstract\s+)?class\s+(\S+)/", function($matchs) use($package, $fname) {
				list($origin, $name) = $matchs;
				$replace = array(
					"/**",
					"* {$fname}",
					"*",
					"* myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework",
					"* Copyright " . date("Y") . " Chen Han",
					"*",
					"* Licensed under The MIT License",
					"*",
					"* @copyright Copyright " . date("Y") . " Chen Han",
					"* @link ",
					"* @since ",
					"* @license http://www.opensource.org/licenses/mit-license.php MIT License",
					"*/",
					"",
					"/**",
					"* {$name}",
					"*",
					"*",
					"* @author " . date("Y") . " Chen Han", 
					"* @package framework.{$package}",
					"* @link ",
					"*/",
					$origin
				);
				$replace = join(PHP_EOL, $replace);
				return $replace;
			}, $content);
		//プロパーティ
		$content = preg_replace_callback("/(?:static\s+)?(?:private|protected|public)\s+(?:static\s+)?\\\$([^;]+)/", function($matchs) use($package) {
				list($origin, $define) = $matchs;
				@list($name, $val_string) = explode(" = ", $define); 
				$type = "";
				if(!empty($val_string)) {
					//ここはデータのタイプを取得ための処理、データタイプの取得が失敗しても問題がありませんので、エラーを出さないようにする
					@eval("\$var =" . $val_string . ";");
					$type = @gettype($var);
				}
				$replace = array(
					"/**",
					"*",
					"*",
					"* @var {$type}",
					"* @link ",
					"*/",
					$origin
				);
				$replace = join(PHP_EOL, $replace);
				return $replace;
			}, $content);
		//メソッド
		$content = preg_replace_callback("/(?:static\s+)?(?:private|protected|public)\s+(?:static\s+)?function\s+([^{]+)/", function($matchs) use($package) {
				$param = "*";
				list($origin, $define) = $matchs;
				$define = str_replace(")", "", trim($define));
				list($name, $args) = explode("(", $define);
				if(!empty($args)) {
					$args = explode(",", $args);
					foreach($args as $arg) {
						@list($aname, $aval) = explode("=", $arg);
						if(empty($aval)) {
							$param .= PHP_EOL . "* @param  {$aname}";
						} else {
							eval("\$_var =" . $aval . ";");
							$_type = gettype($_var);
							if($_type === "NULL") {
								$_type = " ";
							}
							$param .= PHP_EOL . "* @param {$_type} {$aname}";							
						}
					}
				} 
				$replace = array(
					"/**",
					"*@api",
					$param,
					"* @return ",
					"* @link ",
					"*/",
					$origin
				);
				$replace = join(PHP_EOL, $replace);
				return $replace;
			}, $content);
		//clean $content
		$lines = array_map(function($line) {
				return rtrim($line);
			}, explode(PHP_EOL, $content));
		$content = join(PHP_EOL, $lines);
		file_put_contents($file, $content);
	};
}

function generate_test($package) {
	return function($name, $methods) use ($package) {
		global $config;
		$test_dir = $config["test_dir"] . "/" . $package;
		$test_file = $test_dir . "/" . $name . "Test.php";
		$test_class = $name. "Test";
		if(is_file($test_file)) {
			if(!shell::confirm("{$name}_{$package}のテストデータは既に存在している、上書きしますか?")) {
				return $test_file;
			}
		}
		$test_content = array(
			"<?php",
			"",
			"class {$test_class} extends PHPUnit_Framework_TestCase {");
		$test_content[] = "";
		switch($package) {
			case "model":
				$model_file = $config["model_dir"] . "/" . $name . ".model.php";
				$model_class = $name . "_model";
				$DSN = var_export($config["DSN"], true);
				$test_content[] = "";
				$test_content[] = "    protected \$model;";
				$test_content[] = "";
				$test_content[] = "    public static function setUp() {";
				$test_content[] = "        {$model_class}::commit();";
				$test_content[] = "    }";				
				$test_content[] = "";
				$test_content[] = "    public static function tearDown() {";
				$test_content[] = "        {$model_class}::rollback();";
				$test_content[] = "    }";				
				$test_content[] = "";
				$test_content[] = "    public static function setUpBeforeClass() {";
				$test_content[] = "        require '{$model_file}';";
				$test_content[] = "        \$this->model = {$model_class}::connect($DSN);";
				$test_content[] = "    }";
				$test_content[] = "";
				$test_content[] = "    public static function tearDownAfterClass() {";
				$test_content[] = "        {$model_class}::disconnect();";
				$test_content[] = "    }";				
				break;
			case "controller":
				
				break;
			case "view":
				break;
		}
		foreach($methods as $method) {
			$method = ucfirst($method);
			$test_content[] = "";
			$test_content[] = "    public function test{$method}() {";
			$test_content[] = "";
			$test_content[] = "        \$this->assertTrue(false);";
			$test_content[] = "    }";
		}
		$test_content[] = "";
		$test_content[] = "}";
		$test_content = join(PHP_EOL, $test_content);
		file_put_contents($test_file, $test_content);
		return $test_file;
	};
}