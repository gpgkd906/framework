<?php
/**
 * place_address.model.php
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
 * place_address_model
 * Placeの詳細アドレスデータベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_address_model extends Model_core {
	##columns##
    /**
	 * カラム
	 * @api
	 * @var array
	 * @link
	 */
    public $columns = array(
        'id','place_id','pref','address','website'
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
		'pref' => '`pref` varchar(64) NOT NULL',
		'address' => '`address` text NOT NULL',
		'website' => '`website` text NOT NULL',
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
		'place_id' => 'UNIQUE KEY `place_id` (`place_id`,`pref`)',
	);
    /**
	 * プライマリーキー
	 * @api
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`place_address`' => 'id');
    ##indexes##
/**
 * 対応するActiveRecordクラス名
 * @api
 * @var String
 * @link
 */
	public $active_record_name = 'place_address_active_record';
/**
 * 結合情報
 * @api
 * @var Array
 * @link
 */
	public $relation = array();

/**
 * 都道府県テーブル
 * @api
 * @var Array
 * @link
 */
    public static $pref_table = array(
		"北海道", "青森県", "岩手県", "宮城県", "秋田県", "山形県", "福島県", "茨城県", "栃木県", "群馬県", "埼玉県", "千葉県", "東京都", "神奈川県", "新潟県", "富山県", "石川県", "福井県", "山梨県", "長野県", "岐阜県", "静岡県", "愛知県", "三重県", "滋賀県", "京都府", "大阪府", "兵庫県", "奈良県", "和歌山県", "鳥取県", "島根県", "岡山県", "広島県", "山口県", "徳島県", "香川県", "愛媛県", "高知県", "福岡県", "佐賀県", "長崎県", "熊本県", "大分県", "宮崎県", "鹿児島県", "沖縄県", "不明"
	);


/**
 * 指定するプレースのアドレス情報とwebsite情報を更新・追加する
 * @api
 * @param String $place_id Google Map用place_id
 * @param Array $address_components Google Mapで取得した詳細アドレスコンポーネット
 * @param String $website website URL
 * @return
 * @link
 */
    public function upgrade($place_id, $address_components, $website) {
		$data = array(
			"pref" => array_pop(array_intersect($address_components, self::$pref_table)),
			"address" => join(",", $address_components),
			"website" => $website
		);
		return $this->upgrade_by_place_id($place_id, $data);
    }

}

/**
 * place_address_active_record
 * 
 * 
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_address_active_record extends active_record_core {
	###active_define###
/**
 *
 * テーブル名
 * @api
 * @var 
 * @link
 */
	protected static $from = 'place_address';
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
		'pref' => 2,
		'address' => 3,
		'website' => 4,
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