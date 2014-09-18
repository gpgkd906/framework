<?php
/**
 * file_votes.model.php
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
 * file_votes_model
 * 
 * ファイル投票データベース
 *
 * 場所画像投票機能
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class file_votes_model extends model_core {
	##columns##
    /**
	 * カラム
	 * @api
	 * @var array
	 * @link
	 */
	public $columns = array(
        'id','file_id','voter'
    );
    /**
	 * カラム定義
	 * @api
	 * @var array
	 * @link
	 */
	public $alter_columns = array (
		'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
		'file_id' => '`file_id` int(11) NOT NULL',
		'voter' => '`voter` int(11) NOT NULL',
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
		'file_id' => 'UNIQUE KEY `file_id` (`file_id`,`voter`)',
	);
	/**
	 * プライマリーキー
	 * @api
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`file_votes`' => 'id');
    ##indexes##
	/**
	 * 結合情報
	 * @api
	 * @var array
	 * @link
	 */
	public $relation = array();
}