<?php
/**
 * place_types.model.php
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
 * place_types_model
 * 
 * 場所の利用者タイプデータベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class place_types_model extends model_core {
	##columns##
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $columns = array(
        'id','place_id','wheelchair','autowheelchair','walker','babycar','pregnant','vision','hear','ostomate','other'
    );
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $alter_columns = array (
		'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
		'place_id' => '`place_id` varchar(128) NOT NULL',
		'wheelchair' => '`wheelchair` int(11) NOT NULL',
		'autowheelchair' => '`autowheelchair` int(11) NOT NULL',
		'walker' => '`walker` int(11) NOT NULL',
		'babycar' => '`babycar` int(11) NOT NULL',
		'pregnant' => '`pregnant` int(11) NOT NULL',
		'vision' => '`vision` int(11) NOT NULL',
		'hear' => '`hear` int(11) NOT NULL',
		'ostomate' => '`ostomate` int(11) NOT NULL',
		'other' => '`other` int(11) NOT NULL',
	);
    ##columns##
	##indexes##
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $alter_indexes = array (
		'PRIMARY' => 'PRIMARY KEY  (`id`)',
		'place_id' => 'UNIQUE KEY `place_id` (`place_id`)',
	);
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`place_types`' => 'id');
    ##indexes##
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();

    /**
	 * 場所利用者情報データレコードを初期化する
	 * @param String $place_id Google Map用placeId
	 * @return
	 * @link
	 */
	public function initialization($place_id) {
		$record = $this->new_record();
		$record->place_id = $place_id;
		$record->wheelchair = 0;
		$record->autowheelchair = 0;
		$record->walker = 0;
		$record->babycar = 0;
		$record->pregnant = 0;
		$record->vision = 0;
		$record->hear = 0;
		$record->ostomate = 0;
		$record->other = 0;
		$record->save();
	}


    /**
	 * 場所利用者情報データを更新(増加)
	 * @param String $place_id Google Map用placeId
	 * @param Array $type 場所利用者情報データ
	 * @return
	 * @link
	 */
	public function increase_type_by_place_id($place_id, $type) {
		$sql = array("update place_types set");
		$set = array();
		if(in_array($type, $this->columns)) {
			$set[] = "{$type}={$type}+1";
		}
		$sql[] = join(",", $set);
		$sql[] = "where place_id=?";
		$this->query(join(" ", $sql), array($place_id));
    }

    /**
	 * 場所利用者情報データを更新(減少)
	 * @param String $place_id Google Map用placeId
	 * @param Array $type 場所利用者情報データ
	 * @return
	 * @link
	 */
	public function decrease_type_by_place_id($place_id, $type) {
		$sql = array("update place_types set");
		$set = array();
		if(in_array($type, $this->columns)) {
			$set[] = "{$type}={$type}-1";
		}
		$sql[] = join(",", $set);
		$sql[] = "where place_id=?";
		$this->query(join(" ", $sql), array($place_id));
    }

}