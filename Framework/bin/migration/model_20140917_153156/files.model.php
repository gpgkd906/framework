<?php
/**
 * files.model.php
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
 * files_model
 *  
 * ファイルデータベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class files_model extends model_core {
	##columns##
    /**
	 * カラム
	 * @api
	 * @var array
	 * @link
	 */
	public $columns = array(
        'id','file','filename','size','mime','path','link','register_dt','update_dt'
    );
    /**
	 * カラム定義
	 * @api
	 * @var array
	 * @link
	 */
	public $alter_columns = array (
		'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
		'file' => '`file` varchar(255) NOT NULL',
		'filename' => '`filename` varchar(255) NOT NULL',
		'size' => '`size` int(11) NOT NULL',
		'mime' => '`mime` varchar(255) NOT NULL',
		'path' => '`path` varchar(255) NOT NULL',
		'link' => '`link` varchar(255) NOT NULL',
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
		'mime' => ' KEY `mime` (`mime`)',
	);
	/**
	 * プライマリーキー
	 * @api
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`files`' => 'id');
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
	 * 結合情報
	 * @api
	 * @var array
	 * @link
	 */
	public $relation = array();
}