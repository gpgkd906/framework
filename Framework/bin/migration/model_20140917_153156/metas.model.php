<?php
/**
 * metas.model.php
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
 * metas_model
 * 
 * メタ情報データベース。
 *
 * Key/Value型でデータを保存できる
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class metas_model extends model_core {
	##columns##
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $columns = array(
        'id','meta_key','meta_value'
    );
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $alter_columns = array (
		'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
		'meta_key' => '`meta_key` varchar(255) NOT NULL',
		'meta_value' => '`meta_value` longtext NOT NULL',
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
		'meta_key' => 'UNIQUE KEY `meta_key` (`meta_key`)',
	);
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`metas`' => 'id');
    ##indexes##
	##relation##
	/**
	 * @deprecated
	 * @var array
	 * @link
	 */
	public $has_many = array();
	/**
	 * @deprecated
	 * @var array
	 * @link
	 */
	public $belong_to = array();
    ##relation##
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();

	/**
	 * データを読み込む
	 * @param  $key
	 * @return
	 * @link
	 */
	public function read($key) {
	}

	/**
	 * データを更新する
	 * @param  $key
	 * @param   $value
	 * @return
	 * @link
	 */
	public function write($key, $value) {
	}
}