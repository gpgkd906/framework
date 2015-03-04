<?php
/**
 * reviews.model.php
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
 * reviews_model
 * 
 * レビューデータベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class reviews_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','author','content','place_id','entry','step','register_dt','update_dt'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'author' => '`author` int(11) NOT NULL',
  'content' => '`content` longtext NOT NULL',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'entry' => '`entry` int(11) NOT NULL',
  'step' => '`step` int(11) NOT NULL',
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
  'reviews' => 'UNIQUE KEY `reviews` (`author`,`place_id`,`register_dt`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`reviews`' => 'id');
    ##indexes##
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "reviews_active_record";
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();


    /**
	 * レビューを新規追加する
	 * @param Array $raw レビューデータ
	 * @param Integer $author 投稿者id(アカウントid) 
	 * @return
	 * @link
	 */
	public function append($raw, $author) {
		$review = $this->new_record();
		$review->author = $author;
		$review->content = $raw["review"];
		$review->place_id = $raw["place_id"];
		$review->entry = $raw["entry"];
		$review->step = $raw["step"];
		if($review_id = $review->save()) {
			App::model("review_attrs")->append($review_id, $raw["info"]);
			App::model("places")->increase_point_by_place_id($raw["place_id"], $raw);
			App::model("place_attrs")->increase_info_by_place_id($raw["place_id"], $raw["info"]);
			$profile = App::model("profiles")->find_by_account_id($author, true);
			App::model("place_types")->increase_type_by_place_id($raw["place_id"], $profile["type"]);
			return $review_id;
		}
		return false;
    }

    /**
	 * レビューを削除する
	 *
	 * レビュー削除する時はレビュー評価データの削除、または場所の評価データ更新を忘れてはいけません
	 * @param Integer $review_id レビューid
	 * @return
	 * @link
	 */
	public function remove($review_id) {
		if($record = $this->find_by_id($review_id)) {
			self::begin();
			App::model("places")->decrease_point_by_place_id($record->place_id, $record->to_array());
			$profile = App::model("profiles")->find_by_account_id($record->author, true);
			App::model("place_types")->increase_type_by_place_id($raw["place_id"], $profile["type"]);
			if($review_attrs = App::model("review_attrs")->find_by_review_id($review_id)) {
				App::model("place_attrs")->decrease_info_by_place_id($record->place_id, $review_attrs->to_array());
				$review_attrs->delete();
			}
			$record->delete();
			self::commit();
		}
    }

    /**
	 * 指定場所のレビューを取得
	 * @param String $place_id Google Map用PlaceId
	 * @param Integer $offset 取得するレビューの範囲
	 * @return
	 * @link
	 */
	public function get_all_by_place_id($place_id, $offset = null) {
		if($offset) {
			$this->limit($offset, 10);
		} else {
			$this->limit(10);
		}
		return $this->find_all_by_place_id($place_id, true);
    }

    /**
	 * 指定投稿者のレビューを取得
	 * @param Integer $author アカウントid
	 * @param Integer $offset 取得するレビューの範囲
	 * @return
	 * @link
	 */
	public function get_all_by_author($author, $offset = null) {
		if($offset) {
			$this->limit($offset, 10);
		} else {
			$this->limit(10);
		}
		return $this->find_all_by_author($author, true);
    }

    /**
	 * 指定のレビューは指定のユーザーが投稿したかどかのチェック
	 * @param Integer $review_id レビューid
	 * @param Integer $author アカウントid
	 * @return
	 * @link
	 */
	public function match_author($review_id, $author) {
		$this->find("author", $author);
		return (bool) $this->find_by_id($review_id);
    }


    /**
	 * 投稿者の全てのレビューを新着順で取得する
	 *
	 * 最終レビュー時間が必要ですので、サーバー上でソートする必要がある
	 *
	 * このデータはクライアント側でキャッシュすればパフォーマンス的にいいかも(検討) 
	 * @param Integer $author アカウントid
	 * @return
	 * @link
	 */
	public function reviews_history($author) {
		
	
		$this->find("author", $author)->group("place_id")->order("register_dt desc");
		return $this->getAll_as_array();
    }

}

/**
 * reviews_active_record
 * 
 * reviewsデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class reviews_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
protected static $from = 'reviews';
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
  'author' => 1,
  'content' => 2,
  'place_id' => 3,
  'entry' => 4,
  'step' => 5,
  'register_dt' => 6,
  'update_dt' => 7,
);
###active_define###
}