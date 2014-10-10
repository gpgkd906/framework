<?php

/**
 * Config.php
 *
 * myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework
 * Copyright 2014 Chen Han
 *
 * Licensed under The MIT License
 *
 * @copyright Copyright 2014 Chen Han
 * @link
 * @since
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * config
 * フレームワークコンフィグクラス
 *
 * 従来の定数コンフィグを置き換わるもの
 *
 * 一種のメモリーキャッシュとしても使える
 *
 * @author 2014 Chen Han
 * @package framework.core
 * @link
 */
class Config {

	/**
	 * 内部ストレージ
	 * @api
	 * @var array
	 * @link
	 */
	private static $storage = array();

	/**
	 * インスタンス化を阻止する
	 * @return
	 * @link
	 */
	private function __construct() {}

	/**
	 * コンフィグを保存する,上書きできない
	 * @api
	 *
	 * @param String $key コンフィグ名 
	 * @param Mixed $value コンフィグ値
	 * @return
	 * @link
	 */
	public static function register($key, $value) {
		if(!isset(self::$storage[$key])) {
			self::$storage[$key] = $value;
		}
	}

	/**
	 * 指定コンフィグ値を取る
	 * @api
	 *
	 * @param String $key コンフィグ名
	 * @param Mixed $default デフォールトコンフィグ値
	 * @return
	 * @link
	 */
	public static function fetch($key, $default = false) {
		return isset(self::$storage[$key]) ? self::$storage[$key] : $default;
	}

	/**
	 * キャッシュしたコンフィグ配列から指定の値だけを取る
	 * @api
	 *
	 * @param String $key コンフィグ名
	 * @param String $name サブコンフィグ名
	 * @param Mixed $default デフォールトサブコンフィグ値
	 * @return
	 * @link
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