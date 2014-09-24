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
	 * 結合情報
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
		list($config, $complete, $search) = $this->scaffold_action($option);
		$scaffold->on_edit($config, null, $complete);
		$scaffold->on_search($config, $search);
		$scaffold->controls("list", "edit", "search");
		$scaffold->add_mask(array("id" => "システム内部id", "place_id" => "Google Map用id", "name" => "スポット名", "vicinity" => "アドレス", "tel" => "電話番号", "controll" => "操作"));
		$scaffold->add_filter("id", "place_id", "lat", "lng", "reviews_cnt", "entry", "step", "be_edited");
		$scaffold->model($this);
		return $scaffold;
	}

	/**
	 * scaffold処理
	 * @api
	 * @param Array $option
	 * @return
	 * @link
	 */
	public function scaffold_action($option = array()) {
		$config = function($scaffold, $form, $record) {};
		$complete = function($data, $record, $scaffold) use($option) {
			$record->assign($option);
			$record->be_edited = "yes";
			$record->save();
			App::model("place_category")->bind_category($record->place_id, $data["category"]);
		};
		$search = function($search, $model) {
			if(isset($search["category"])) {
				$tmp = array_column(App::model("place_category")->find_all_by_category($search["category"], true), "place_id");
				$model->add_filter("place_id", "place_id", array_unique($tmp));
				unset($search["category"]);
			}
			foreach($search as $key => $value) {
				$model->add_filter($key, $key, $value, "like");
			}
		};
		return array($config, $complete, $search);
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
	protected static $from = 'places';
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
	public static $category_table = array("airport" => "空港", 
		"amusement_park" => "遊園地", 
		"aquarium" => "水族館", 
		"art_gallery" => "アート ギャラリー", 
		"bakery" => "ベーカリー、パン屋", 
		"bank" => "銀行", 
		"bar" => "居酒屋", 
		"beauty_salon" => "ビューティー サロン", 
		"bicycle_store" => "自転車店", 
		"book_store" => "書店", 
		"bowling_alley" => "ボウリング場", 
		"bus_station" => "バスターミナル", 
		"cafe" => "カフェ", 
		"campground" => "キャンプ場", 
		"car_dealer" => "カー ディーラー", 
		"car_rental" => "レンタカー", 
		"car_repair" => "車の修理", 
		"car_wash" => "洗車場", 
		"cemetery" => "墓地", 
		"church" => "教会", 
		"city_hall" => "市役所", 
		"clothing_store" => "衣料品店", 
		"convenience_store" => "コンビニエンス ストア", 
		"courthouse" => "裁判所", 
		"dentist" => "歯科医", 
		"department_store" => "百貨店", 
		"doctor" => "医者", 
		"electronics_store" => "電器店", 
		"embassy" => "大使館", 
		"finance" => "金融業", 
		"fire_station" => "消防署", 
		"florist" => "花屋", 
		"food" => "食料品店", 
		"funeral_home" => "葬儀場", 
		"furniture_store" => "家具店", 
		"gas_station" => "ガソリンスタンド", 
		"grocery_or_supermarket" => "スーパー", 
		"gym" => "スポーツクラブ", 
		"hair_care" => "ヘアケア", 
		"hardware_store" => "金物店", 
		"home_goods_store" => "インテリア ショップ", 
		"hospital" => "病院", 
		"insurance_agency" => "保険代理店", 
		"jewelry_store" => "宝飾店", 
		"laundry" => "クリーニング店", 
		"library" => "図書館", 
		"liquor_store" => "酒店", 
		"local_government_office" => "役場", 
		"locksmith" => "錠屋", 
		"lodging" => "宿泊施設", 
		"meal_delivery" => "出前", 
		"meal_takeaway" => "テイクアウト", 
		"movie_rental" => "DVD レンタル", 
		"movie_theater" => "映画館", 
		"museum" => "美術館/博物館", 
		"night_club" => "ナイト クラブ", 
		"park" => "公園", 
		"parking" => "駐車場", 
		"pet_store" => "ペット ショップ", 
		"pharmacy" => "薬局", 
		"place_of_worship" => "礼拝所", 
		"police" => "警察", 
		"post_office" => "郵便局", 
		"real_estate_agency" => "不動産業", 
		"restaurant" => "レストラン", 
		"school" => "学校", 
		"shoe_store" => "靴屋", 
		"shopping_mall" => "ショッピング モール", 
		"spa" => "温泉、スパ", 
		"store" => "小売店", 
		"train_station" => "駅", 
		"travel_agency" => "旅行代理店", 
		"veterinary_care" => "獣医", 
		"zoo" => "動物園");
	public function category($mode = "string") {
		switch($mode) {
			case "string":
				$category_ids = array_column(App::model("place_category")->find_all_by_place_id($this->place_id, true), "category");
				$tmp = array_column(App::model("category")->find_all_by_id($category_ids, true), "name");
				$names = array();
				foreach($tmp as $key => $label) {
					if(isset(self::$category_table[$label])) {
						$names[] =  self::$category_table[$label];
					}
				}
				return join("、", $names);
				break;
			case "array": 
				return array_column(App::model("place_category")->find_all_by_place_id($this->place_id, true), "category");
				break;
		}
	}
	
}