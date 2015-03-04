<?php
/**
 * metas.model.php
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
 * metas_model
 * 
 * メタ情報データベース。
 *
 * Key/Value型でデータを保存できる
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class metas_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','meta_key','meta_value'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'meta_key' => '`meta_key` varchar(255) NOT NULL',
  'meta_value' => '`meta_value` longtext NOT NULL',
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
  'meta_key' => 'UNIQUE KEY `meta_key` (`meta_key`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`metas`' => 'id');
    ##indexes##
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "metas_active_record";
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();

	/**
	 * データを読み込む
	 * @param  $key
	 * @return
	 * @link
	 */
	public function read($key) {
	}

	/**
	 * データを更新する
	 * @param  $key
	 * @param   $value
	 * @return
	 * @link
	 */
	public function write($key, $value) {
	}
}

/**
 * metas_active_record
 * 
 * metasデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class metas_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
protected static $from = 'metas';
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
  'meta_key' => 1,
  'meta_value' => 2,
);
/**
* 遅延静的束縛：現在のActiveRecordのカラムにあるかどか
* @api
* @param   
* @param    
* @return
* @link
*/
public static function has_column($col) {
	return isset(self::$store_schema[$col]);
}
/**
* 遅延静的束縛：ActiveRecordのテーブル名を取得
* @api
* @param   
* @param    
* @return
* @link
*/
public static function get_from() {
	return self::$from;
}
/**
* 遅延静的束縛：ActiveRecordのプライマリーキーを取得
* @api
* @param   
* @param    
* @return
* @link
*/
public static function get_primary_key() {
	return self::$primary_key;
}
###active_define###
}