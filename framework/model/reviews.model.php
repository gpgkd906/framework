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
	 * 結合情報
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

	/**
	 * 指定する投稿者のレビューの統計情報
	 *
	 * まずは投稿者の全てのレビューを取得して統計する
	 *
	 * こうすることで、初期化で取得するデータの量が大分減るので
	 *
	 * もし何万件も投稿するユーザーが存在するようなサービスになれば
	 *
	 * ここの統計処理をまた細かく分解して、別々のapiで再構築することでパフォーマンス改善ができる
	 *
	 * 次は誰がこのアプリの開発を継続するのかは知らないのが、apiを分解するときはカテゴリ、地域、ランキングで分ければいいですよ。
	 *
	 * あるいは全てを0から再構築するのが必要性はないでしょう、現設計では柔軟性が足りないと感じる時以外では再構築をお勧めしないからだ。
	 *
	 * 忠告は以上だ
	 * 
	 * @api
	 * @param Integer $author 投稿者名
	 * @return
	 * @link
	 */
    public function statistics ($author) {
		App::model("place_address");
		$category = App::model("category")->getall_as_array();
		$this->statistics = array(
			"category" => array_fill_keys(array_column($category, "id"), 0),
			"area" => array_fill_keys(place_address_model::$pref_table, 0),
			"rank" => array(null, null, null, null, null),
			"review_place" => array(),
		);
		$this->find("author", $author)->each_array(function($row) {
				$this->statistics["review_place"][$row["id"]] = $row["place_id"];
			});	
		$unique_place_id = array_unique($this->statistics["review_place"]);
		//カテゴリ
		$tmp = App::model("place_category")->find_all_by_place_id($unique_place_id, true);
		$category_set = array_fill_keys(array_column($tmp, "place_id"), array());
		foreach($tmp as $row) {
			$category_set[$row["place_id"]][] = $row["category"];
		}		
		//地域
		$tmp = App::model("place_address")->find_all_by_place_id($unique_place_id, true);
		$area_set = array_column($tmp, "pref", "place_id");
		
		foreach($this->statistics["review_place"] as $review_id => $place_id) {
			//カテゴリ集計
			$cates = $category_set[$place_id] ? : array();
			foreach($cates as $c) {
				$this->statistics["category"][$c] ++;
			}
			//地域集計
			$area = $area_set[$place_id] ? : "不明";
			$this->statistics["area"][$area] ++;
		}
		
		//無駄を省く
		$this->statistics["category"] = array_diff($this->statistics["category"], array(0));
		$this->statistics["area"] = array_diff($this->statistics["area"], array(0));
		//人間が読みやすいように
		$category = array_column($category, "name", "id");
		foreach($this->statistics["category"] as $cid => $cnt) {
			unset($this->statistics["category"][$cid]);
			$this->statistics["category"][$category[$cid]] = $cnt;
		}
		
		//いいねランキング
		$tmp = App::model("interesting")->count_value_by_review_id(array_unique(array_keys($this->statistics["review_place"])));
		$id_rank = array();
		foreach($tmp as $id => $cnt) {
			$id_rank["r_" . strval($id)] = intval($cnt);
		}
		arsort($id_rank);
		$rid_rank = array_map(function($label) {
				return array_pop(explode("_", $label));
			}, array_keys(array_slice($id_rank, 0, 5)));
		$reviews = $this->find_all_by_id($rid_rank, true);
		$rank_place_id = array_unique(array_column($reviews, "place_id"));
		$places = App::model("places")->find_all_by_place_id($rank_place_id, true);
		$place_category = App::model("place_category")->find_all_by_place_id($rank_place_id, true);
		$category_name = App::model("category")->find_all_by_id(array_column($place_category, "category"), true);
		$this->statistics["rank"] = array(
			"id_rank" => $id_rank,
			"reviews" => $reviews,
			"places" => $places,
			"place_category" => $place_category,
			"category_name" => $category_name
		);
		unset($this->statistics["review_place"]);
		return $this->statistics;
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