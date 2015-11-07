<?php

class shell {

	private static $error_log = "/dev/null";
	
	public static function set_log($log) {
		self::$error_log = $log;
	}

	public static function process($cmd) {
		$cmd = escapeshellcmd($cmd);
		$descriptorspec = array(
			0 => array("pipe", "r"),
			1 => array("pipe", "w"),
			2 => array("file", self::$error_log, "a")
		);
		$process = proc_open($cmd, $descriptorspec, $pipes);
		$content = stream_get_contents($pipes[1]);
		fclose($pipes[0]);
		fclose($pipes[1]);
		$res = proc_close($process);
		return $content;
	}
	
	public static function message($cmd) {
		$cmd = escapeshellcmd($cmd);
		$cmd = "nohup " . $cmd . " > " . self::$error_log . " &";
		exec($cmd);
	}

	public static function exec($cmd) {
		exec(escapeshellcmd($cmd));
	}

	public static function copy_dir($from, $to) { 
		self::exec("cp -R {$from} {$to}");
	}
	
	public static function ls($dir, $func, $pass = array()) {
		$_check = preg_replace("/\/$/", "", $dir);
		if(in_array($_check, $pass)) {
			return false;
		}
		if(is_dir($dir)) {
			$handler = opendir($dir);
			while($file = readdir($handler)) {
				if($file === "." || $file === ".." || preg_match("/~$/", $file)) {
					continue;
				}
				$_file = str_replace("//", "/", $dir . "/" . $file);
				if(is_dir($_file)) {
					call_user_func("self::ls", $_file, $func, $pass);
				} else {
					if(in_array($_file, $pass)) {
						//skip the file
						continue;
					}
					call_user_func($func, $file, $_file);
				}
			}
		}
	}
	
	public static function read($message = null, $nullable = true) {
		if($nullable) {
			echo $message;
			return trim(fgets(STDIN));
		} else {
			do {
				echo $message;
				$input = trim(fgets(STDIN));
			} while(!$input);
			return $input;
		}
	}

	public static function confirm($message) {
		$confirm = strtolower(self::read($message . "[Y/n]?"));
		if($confirm === "y" || $confirm === "yes" || empty($confirm)) {
			return true;
		} else {
			return false;
		}
	}

	public static function read_select($message, $options = array()) {
		$new = array($message);
		$cnt = 1;
		foreach($options as $option) {
			$new[] = "{$cnt}. " . $option;
			$cnt++;
		}
		$cnt--;
		echo join(PHP_EOL, $new), PHP_EOL;
		do {
			$choose = strtolower(self::read("choose one from [1-{$cnt}] :")) | 0;
		} while( $choose < 1 && $choose > $cnt );
		return $options[$choose - 1];
	}
	
	public static function get_args($default = null, $aliases = null, $usage = null) {
		global $argv;
		$res = array();
		$size = count($argv);
		if($size === 1) {
			if(!empty($usage)) {
				echo "{$argv[0]} Usage :", PHP_EOL;
				foreach($usage as $key => $use) {
					echo "  argument: {$key}", PHP_EOL;
					echo "    you can use value as those: ", "[", join(", ", $use), "]", PHP_EOL;
				}
				die();
			}
		}
		if($aliases === null) {
			$aliases = array();
		}
		$cnt = 0;
		for($i = 1; $i < $size; $i++) {
			@list($k, $v) = explode("=", $argv[$i]);
			if(isset($v)) {
				$k = preg_replace("/^-*/", "", $k);
				$k = isset($aliases[$k]) ? $aliases[$k] : $k;
				$res[$k] = $v;
			} else {
				if(isset($aliases[$cnt])) {
					$res[$aliases[$cnt]] = $k;
				} else {
					$res[] = $k;
				}
				$cnt++;
			}
		}
		if($default !== null && is_array($default)) {
			$res = array_merge($default, $res);
		}
		return $res;
	}
	
}