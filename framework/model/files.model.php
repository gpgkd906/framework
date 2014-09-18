<?php
/**
 * files.model.php
 *
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
 * files_model
 *  
 * ファイルデータベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class files_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','file','filename','size','mime','path','link','register_dt','update_dt'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'file' => '`file` varchar(255) NOT NULL',
  'filename' => '`filename` varchar(255) NOT NULL',
  'size' => '`size` int(11) NOT NULL',
  'mime' => '`mime` varchar(255) NOT NULL',
  'path' => '`path` varchar(255) NOT NULL',
  'link' => '`link` varchar(255) NOT NULL',
  'register_dt' => '`register_dt` bigint(20) NOT NULL',
  'update_dt' => '`update_dt` bigint(20) NOT NULL',
);
    ##columns##
	##indexes##
    /**
    * インデックス定義
    * @api
    * @var array
    * @link
    */
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'mime' => ' KEY `mime` (`mime`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`files`' => 'id');
    ##indexes##
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "files_active_record";
	/**
	 * 結合情報
	 * @api
	 * @var array
	 * @link
	 */
	public $relation = array();
}

/**
 * files_active_record
 * 
 * filesデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class files_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
protected static $from = 'files';
/**
*
* プライマリキー
* @api
* @var 
* @link
*/
protected static $primary_key = 'id';
/**
* モデルのカラムの反転配列。
* 
* 反転後issetが働ける、パフォーマンス的にいい
*
* 反転は自動生成するので，実行時に影響はありません
* @api
* @var 
* @link
*/
protected static $store_schema = array (
  'id' => 0,
  'file' => 1,
  'filename' => 2,
  'size' => 3,
  'mime' => 4,
  'path' => 5,
  'link' => 6,
  'register_dt' => 7,
  'update_dt' => 8,
);
/**
* 遅延静的束縛：現在のActiveRecordのカラムにあるかどか
* @api
* @param String $col チェックするカラム名
* @return
* @link
*/
public static function has_column($col) {
	return isset(self::$store_schema[$col]);
}
/**
* 遅延静的束縛：ActiveRecordのテーブル名を取得
* @api
* @return
* @link
*/
public static function get_from() {
	return self::$from;
}
/**
* 遅延静的束縛：ActiveRecordのプライマリーキーを取得
* @api
* @return
* @link
*/
public static function get_primary_key() {
	return self::$primary_key;
}
###active_define###
}