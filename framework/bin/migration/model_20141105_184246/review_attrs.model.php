<?php
/**
 * review_attrs.model.php
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
 * review_attrs_model
 * レビューで投稿された評価データベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class review_attrs_model extends Model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','review_id','toilet','space','flat','elevator','parking','quiet','ostomate','baby','socket','smoking'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'review_id' => '`review_id` int(11) NOT NULL',
  'toilet' => '`toilet` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'space' => '`space` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'flat' => '`flat` enum(\'yes\',\'no\') NULL Default \'no\'',
  'elevator' => '`elevator` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'parking' => '`parking` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'quiet' => '`quiet` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'ostomate' => '`ostomate` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'baby' => '`baby` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'socket' => '`socket` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'smoking' => '`smoking` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
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
  'review_id' => 'UNIQUE KEY `review_id` (`review_id`,`toilet`,`space`,`flat`,`elevator`,`parking`,`quiet`,`ostomate`,`baby`,`socket`,`smoking`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`review_attrs`' => 'id');
    ##indexes##
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "review_attrs_active_record";
	/**
	 * 結合情報
	 * @var array
	 * @link
	 */
	public $relation = array();


    /**
	 * レビューの評価を追加する
	 * @param Integer $review_id レビューid
	 * @param Array $attrs 評価データ
	 * @return
	 * @link
	 */
	public function append($review_id, $attrs) {
		$record = $this->new_record();
		foreach($this->columns as $col) {
			if(in_array($col, $attrs)) {
				$record->$col = "yes";
			} else {
				$record->$col = "no";
			}
		}
		$record->review_id = $review_id;
		$record->save();
    }

    /**
	 * レビューの評価を削除する
	 * @param Integer $review_id レビューid
	 * @return
	 * @link
	 */
	public function remove($review_id) {
		if($record = $this->find_by_review_id($review_id)) {
			$record->delete();
		}
    }


}

/**
 * review_attrs_active_record
 * 
 * review_attrsデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class review_attrs_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
protected static $from = 'review_attrs';
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
  'review_id' => 1,
  'toilet' => 2,
  'space' => 3,
  'flat' => 4,
  'elevator' => 5,
  'parking' => 6,
  'quiet' => 7,
  'ostomate' => 8,
  'baby' => 9,
  'socket' => 10,
  'smoking' => 11,
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