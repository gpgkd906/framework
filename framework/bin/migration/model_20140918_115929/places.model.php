<?php
/**
 * places.model.php
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
 * places_model
 * 
 * 場所データベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class places_model extends model_core {
	##columns##
    /**
	 * カラム
	 * @api
	 * @var array
	 * @link
	 */
    public $columns = array(
        'id','place_id','lat','lng','name','vicinity','tel','reviews_cnt','entry','step','be_edited'
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
		'lat' => '`lat` double NOT NULL',
		'lng' => '`lng` double NOT NULL',
		'name' => '`name` varchar(256) NOT NULL',
		'vicinity' => '`vicinity` varchar(256) NOT NULL',
		'tel' => '`tel` varchar(64) NULL',
		'reviews_cnt' => '`reviews_cnt` int(11) NOT NULL',
		'entry' => '`entry` int(11) NOT NULL',
		'step' => '`step` int(11) NOT NULL',
		'be_edited' => '`be_edited` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
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
		'reference' => 'UNIQUE KEY `reference` (`place_id`)',
	);
    /**
	 * プライマリーキー
	 * @api
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`places`' => 'id');
    ##indexes##
	/**
	 * 対応するActiveRecordクラス名
	 * @api
	 * @var String
	 * @link
	 */
	public $active_record_name = "places_active_record";
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();

	/**
	 * 場所データ照合
	 * 
	 * Google Mapで取得したデータとサーバー上保存されたデータを比較する
	 *
	 * サーバー上データを既に取得しているのであれば，データを統合する
	 *
	 * サーバー上データがない場合はデータを保存する
	 *
	 * @param Array $places Google Mapで取得した場所データ
	 * @return
	 * @link
	 */
	public function compare_by_places($places) {

		$place_ids = array_column($places, "place_id");

		$tmp = array();

		foreach($places as $place) {

			$tmp[$place["place_id"]] = $place;

		}

		$places = $tmp;

		$exists = $this->find_all_by_place_id($place_ids);

		foreach($exists as $record) {

			$key = $record->place_id;

			$places[$key] = array_merge($places[$key], $record->to_array());

			unset($tmp[$key]);
		}

		$creates = $this->create_by_places($tmp);

		foreach($creates as $record) {

			$key = $record->place_id;

			$places[$key] = array_merge($places[$key], $record->to_array());

		}

		return $places;
	}

	/**
	 * 場所データを新規保存する
	 *
	 * 保存する時は対応する以下の一連データも初期化する
	 * 
	 * 場所の評価、場所の利用者情報、場所のカテゴリ
	 *
	 * @param Array $places Google Mapで取得した場所データ
	 * @return
	 * @link
	 */
	public function create_by_places($places) {
		$creates = array();

		foreach($places as $key => $place) {

			$place["be_edited"] = "no";

			$place["reviews_cnt"] = 0;

			$place["entry"] = 0;

			$place["step"] = 0;

			$creates[] = $this->create_record($place);

			App::model("place_attrs")->initialization($place["place_id"]);

			App::model("place_types")->initialization($place["place_id"]);

			$cate_ids = App::model("category")->map_category($place["types"]);

			App::model("place_category")->bind_category($place["place_id"], $cate_ids);
		}
		return $creates;
	}

	/**
	 * 場所に投稿が新規追加される時
	 *
	 * 場所のメイン評価データを更新する(増加)
	 * @param String $place_id Google Map用PlaceId
	 * @param Array $review 投稿されたレビュー情報
	 * @return
	 * @link
	 */
	public function increase_point_by_place_id($place_id, $review) {

		$entry = $review["entry"] ? $review["entry"] : 0;

		$step = $review["step"] ? $review["step"] : 0;

		if($record = $this->find_by_place_id($place_id)) {

			$record->entry = $record->entry + $entry;

			$record->step = $record->step + $step;

			$record->reviews_cnt = $record->reviews_cnt + 1;

			$record->save();

		}

	}

	/**
	 * 場所に投稿が削除される時
	 *
	 * 場所のメイン評価データを更新する(減少)
	 * @param String $place_id Google Map用PlaceId
	 * @param Array $review 削除されたレビュー情報
	 * @return
	 * @link
	 */
	public function decrease_point_by_place_id($place_id, $review) {

		$entry = $review["entry"] ? $review["entry"] : 0;

		$step = $review["step"] ? $review["step"] : 0;

		if($record = $this->find_by_place_id($place_id)) {

			$record->entry = $record->entry - $entry;

			$record->step = $record->step - $step;

			$record->reviews_cnt = $record->reviews_cnt - 1;

			$record->save();

		}

	}

}

/**
 * places_active_record
 * 
 * placesデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class places_active_record extends active_record_core {
	###active_define###
/**
 *
 * テーブル名
 * @api
 * @var 
 * @link
 */
	private static $from = 'places';
/**
 *
 * プライマリキー
 * @api
 * @var 
 * @link
 */
	private static $primary_key = 'id';
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
	private static $store_schema = array (
		'id' => 0,
		'place_id' => 1,
		'lat' => 2,
		'lng' => 3,
		'name' => 4,
		'vicinity' => 5,
		'tel' => 6,
		'reviews_cnt' => 7,
		'entry' => 8,
		'step' => 9,
		'be_edited' => 10,
	);
	###active_define###
}