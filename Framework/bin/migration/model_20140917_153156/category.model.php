<?php
/**
 * category.model.php
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
 * category_model
 * 
 * カテゴリデータ構造
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class category_model extends model_core {
	##columns##
    /**
	 * カラム
	 * @api
	 * @var array
	 * @link
	 */
	public $columns = array(
        'id','name'
    );
    /**
	 * カラム定義
	 * @api
	 * @var array
	 * @link
	 */
	public $alter_columns = array (
		'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
		'name' => '`name` varchar(255) NOT NULL',
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
		'cname' => 'UNIQUE KEY `cname` (`name`)',
	);
	/**
	 * プライマリーキー
	 * @api
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`category`' => 'id');
    ##indexes##
	/**
	 * 結合情報
	 * @api
	 * @var array
	 * @link
	 */
	public $relation = array();

    /**
	 * マッピングするカテゴリ名
	 * @api 
	 * @param Array names
	 * @return
	 * @link
	 */
	public function map_category ($names) {

		$tmp = $this->find_all_by_name($names, true);

		$ids = array_column($tmp, "id");

		$_names = array_column($tmp, "name");

		$diffs = array_diff($names, $_names);

		foreach($diffs as $diff) {

			$ids[] = $this->create_record(array("name" => $diff))->id;

		}

		return $ids;
    }


}