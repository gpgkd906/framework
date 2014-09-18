<?php
/**
 * place_images.model.php
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
 * place_images_model
 * 
 * 場所画像データベース
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_images_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','place_id','author','file_id','type','vote'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'author' => '`author` int(11) NOT NULL',
  'file_id' => '`file_id` int(11) NOT NULL',
  'type' => '`type` varchar(64) NOT NULL',
  'vote' => '`vote` int(11) NOT NULL',
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
  'place_id' => 'UNIQUE KEY `place_id` (`place_id`,`author`,`file_id`,`vote`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`place_images`' => 'id');
    ##indexes##
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "place_images_active_record";
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();


    /**
	 * 画像投票数を増加させる
	 * @param String $place_id Google Map用placeId
	 * @param Integer $file_id システム内容用ファイルid
	 * @return
	 * @link
	 */
	public function increase_vote($place_id, $file_id) {
		$this->query("update place_images set vote=vote+1 where place_id=? and file_id=?", array($place_id, $file_id));
		return $this->get_vote($place_id, $file_id);
    }


    /**
	 * 画像投票数を減少させる
	 * @param String $place_id Google Map用placeId
	 * @param Integer $file_id システム内容用ファイルid
	 * @return
	 * @link
	 */
	public function decrease_vote($place_id, $file_id) {
		$this->query("update place_images set vote=vote-1 where place_id=? and file_id=?", array($place_id, $file_id));
		return $this->get_vote($place_id, $file_id);
    }


    /**
	 * 画像投票数を取得
	 * 
	 * 取得時は投票数をチェックする、無効のデータがある場合を自動修正する
	 * @param String $place_id Google Map用placeId
	 * @param Integer $file_id システム内容用ファイルid
	 * @return
	 * @link
	 */
	public function get_vote($place_id, $file_id) {
		$record = $this->find("place_id", $place_id)->find("file_id", $file_id)->get_as_array();
		if($record["vote"] < 0) {
			$this->query("update place_images set vote=0 where place_id=? and file_id=?", array($place_id, $file_id));
			$record["vote"] = 0;
		}
		return $record["vote"];
    }


}

/**
 * place_images_active_record
 * 
 * place_imagesデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_images_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
protected static $from = 'place_images';
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
  'place_id' => 1,
  'author' => 2,
  'file_id' => 3,
  'type' => 4,
  'vote' => 5,
);
###active_define###
}