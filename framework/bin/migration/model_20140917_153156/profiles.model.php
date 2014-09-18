<?php
/**
 * profiles.model.php
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
 * profiles_model
 * 
 * アカウントプロフィールデータベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class profiles_model extends model_core {
	##columns##
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $columns = array(
        'id','account_id','firstname','lastname','firstkana','lastkana','postzip','birth','face','address','sex','type'
    );
    /**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $alter_columns = array (
		'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
		'account_id' => '`account_id` int(11) NOT NULL',
		'firstname' => '`firstname` varchar(255) NOT NULL',
		'lastname' => '`lastname` varchar(255) NOT NULL',
		'firstkana' => '`firstkana` varchar(255) NOT NULL',
		'lastkana' => '`lastkana` varchar(255) NOT NULL',
		'postzip' => '`postzip` varchar(255) NOT NULL',
		'birth' => '`birth` date NOT NULL',
		'face' => '`face` longblob NOT NULL',
		'address' => '`address` varchar(255) NOT NULL',
		'sex' => '`sex` enum(\'男性\',\'女性\',\'秘密\') NOT NULL Default \'秘密\'',
		'type' => '`type` varchar(255) NOT NULL',
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
		'account_id' => 'UNIQUE KEY `account_id` (`account_id`)',
	);
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $primary_keys = array('`profiles`' => 'id');
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
	 * @var array
	 * @link
	 */
	public $relation = array();

	/**
	 * 
	 * @var array
	 * @link
	 */
	public $acts_as = array("api");
}