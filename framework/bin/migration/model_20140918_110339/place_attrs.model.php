<?php
/**
 * place_attrs.model.php
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
 * place_attrs_model
 * 
 * 場所の評価データベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_attrs_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','place_id','toilet','space','flat','elevator','parking','quiet','ostomate','baby','socket','smoking'
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
  'toilet' => '`toilet` int(11) NOT NULL',
  'space' => '`space` int(11) NOT NULL',
  'flat' => '`flat` int(11) NOT NULL',
  'elevator' => '`elevator` int(11) NOT NULL',
  'parking' => '`parking` int(11) NOT NULL',
  'quiet' => '`quiet` int(11) NOT NULL',
  'ostomate' => '`ostomate` int(11) NOT NULL',
  'baby' => '`baby` int(11) NOT NULL',
  'socket' => '`socket` int(11) NOT NULL',
  'smoking' => '`smoking` int(11) NOT NULL',
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
  'place_id' => 'UNIQUE KEY `place_id` (`place_id`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`place_attrs`' => 'id');
    ##indexes##
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();


    /**
	 * データレコードを初期化する
	 * @param String $place_id Google Map用PlaceId
	 * @return
	 * @link
	 */
	public function initialization($place_id) {
		$record = $this->new_record();
		$record->place_id = $place_id;
		$record->toilet = 0;
		$record->space = 0;
		$record->flat = 0;
		$record->elevator = 0;
		$record->parking = 0;
		$record->quiet = 0;
		$record->ostomate = 0;
		$record->baby = 0;
		$record->socket = 0;
		$record->smoking = 0;
		$record->save();
	}

    /**
	 * レビューが投稿される場合
	 *
	 * 場所の評価データを更新(増加)
	 * @param String $place_id Google Map用PlaceId
	 * @param Array $info 場所評価データ
	 * @return
	 * @link
	 */
	public function increase_info_by_place_id($place_id, $info) {
		$sql = array("update place_attrs set");
		$set = array();
		foreach($this->columns as $col) {
			if(in_array($col, $info)) {
				$set[] = "{$col}={$col}+1";
			}
		}
		$sql[] = join(",", $set);
		$sql[] = "where place_id=?";
		$this->query(join(" ", $sql), array($place_id));
	}

/**
 * レビューが削除される場合
 *
 * 場所の評価データを更新(減少)
 * @param String $place_id Google Map用PlaceId
 * @param Array $info 場所評価データ
 * @return
 * @link
 */
	public function decrease_info_by_place_id($place_id, $info) {
		unset($info["id"]);
		$sql = array("update place_attrs set");
		$set = array();
		foreach($this->columns as $col) {
			if(isset($info[$col]) && $info[$col] === "yes") {
				$set[] = "{$col}={$col}-1";
			}
		}
		$sql[] = join(",", $set);
		$sql[] = "where place_id=?";
		$this->query(join(" ", $sql), array($place_id));
    }

}

/**
 * place_attrs_active_record
 * 
 * place_attrsデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_attrs_active_record extends active_record_core {
	###active_define###
	###active_define###
}