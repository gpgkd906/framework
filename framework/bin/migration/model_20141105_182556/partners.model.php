<?php
/**
 * partners.model.php
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
 * partners_model
 *
 * 
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class partners_model extends Model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','name','descript','keyword'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'name' => '`name` varchar(64) NOT NULL',
  'descript' => '`descript` text NOT NULL',
  'keyword' => '`keyword` varchar(64) NOT NULL',
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
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`partners`' => 'id');
    ##indexes##
/**
 * 対応するActiveRecordクラス名
 * @api
 * @var String
 * @link
 */
	public $active_record_name = 'partners_active_record';
/**
 * 結合情報
 * @api
 * @var Array
 * @link
 */
	public $relation = array();

	/**
	 * scaffold設定 
	 *
	 * @api
	 * @param Array $option
	 * @return
	 * @link
	 */
	public function scaffold($option = array()) {
		foreach($option as $key => $value) {
			$this->add_filter($key, $key, $value);
		}
		$scaffold = App::helper("scaffold");
		$scaffold->controls("list", "search", "new", "delete", "edit");
		$scaffold->add_mask(array("name" => "パートナー名", "descript" => "パートナー説明", "keyword" => "検索キーワード"));
		$scaffold->add_filter("id");
		$scaffold->model($this);
		return $scaffold;
	}

}

/**
 * partners_active_record
 * 
 * 
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class partners_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
protected static $from = 'partners';
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
  'name' => 1,
  'descript' => 2,
  'keyword' => 3,
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