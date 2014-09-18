<?php
/**
 * interesting.model.php
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
 * interesting_model
 * 
 * レビューに対す評価データベース
 *
 * レビューに対する「役に立った」、「役に立たなかった」の評価用データベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class interesting_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','review_id','type','sender_id','register_dt','update_dt'
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
  'type' => '`type` enum(\'good\',\'bad\') NOT NULL Default \'good\'',
  'sender_id' => '`sender_id` int(11) NOT NULL',
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
  'review_id' => 'UNIQUE KEY `review_id` (`review_id`,`sender_id`,`type`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`interesting`' => 'id');
    ##indexes##
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "interesting_active_record";
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();


    /**
	 * レビューに対す評価を追加する
	 * @param Array $interesting 評価情報
	 * @return
	 * @link
	 */
	public function append($interesting) {

		$record = $this->new_record();

		$record->assign($interesting);

		return $record->save();
    }


    /**
	 * レビューに対する評価を削除する
	 * @param Array $interesting 評価情報
	 * @return
	 * @link
	 */
	public function remove($interesting) {

		$this->find("review_id", $interesting["review_id"]);

		$this->find("sender_id", $interesting["sender_id"]);

		$this->find("type", $interesting["type"]);

		if($record = $this->get()) {

			$record->delete();

			return true;

		}

		return false;
    }
}

/**
 * interesting_active_record
 * 
 * interestingデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class interesting_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
protected static $from = 'interesting';
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
  'type' => 2,
  'sender_id' => 3,
  'register_dt' => 4,
  'update_dt' => 5,
);
###active_define###
}