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
 * 
 * レビューで投稿された評価データベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class review_attrs_model extends model_core {
	##columns##
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $columns = array(
        'id','review_id','toilet','space','flat','elevator','parking','quiet','ostomate','baby','socket','smoking'
    );
    /**
	 *
	 *
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
	 *
	 *
	 * @var array
	 * @link
	 */
	public $alter_indexes = array (
		'PRIMARY' => 'PRIMARY KEY  (`id`)',
		'review_id' => 'UNIQUE KEY `review_id` (`review_id`,`toilet`,`space`,`flat`,`elevator`,`parking`,`quiet`,`ostomate`,`baby`,`socket`,`smoking`)',
	);
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`review_attrs`' => 'id');
    ##indexes##
	/**
	 *
	 *
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