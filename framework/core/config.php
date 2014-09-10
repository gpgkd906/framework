<?php

class config {
  
  private static $storage = array();
  
  private function __construct() {}
  
  /**
   * 設定キャッシュに設定値を保存する,上書きできない
   */
  public static function register($key, $value) {
	  if(!isset(self::$storage[$key])) {
		  self::$storage[$key] = $value;
	  }
  }
  
  /**
   * キャッシュしたデータを取る
   */
  public static function fetch($key, $default = false) {
	  return isset(self::$storage[$key]) ? self::$storage[$key] : $default;
  }
  
  /**
   * キャッシュした配列から指定の値だけを取る
   */
  public static function search($key, $name, $default = null) {
	  if(isset(self::$storage[$key])) {
		  $storage = self::$storage[$key];
		  if(isset($storage[$name])) {
			  return $storage[$name];
		  }
	  }
	  return $default;
  }
}