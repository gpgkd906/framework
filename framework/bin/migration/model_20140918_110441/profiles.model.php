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
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','account_id','firstname','lastname','firstkana','lastkana','postzip','birth','face','address','sex','type'
    );
    /**
    * カラム定義
    * @api
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
    * インデックス定義
    * @api
    * @var array
    * @link
    */
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'account_id' => 'UNIQUE KEY `account_id` (`account_id`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`profiles`' => 'id');
    ##indexes##
	/**
	 * 結合情報
	 * @var array
	 * @link
	 */
	public $relation = array();

	/**
	 * ビヘイビア設定
	 * @var array
	 * @link
	 */
	public $acts_as = array("api");
}

/**
 * profiles_active_record
 * 
 * profilesデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class profiles_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
private static profiles = 'profiles';
/**
*
* プライマリキー
* @api
* @var 
* @link
*/
private static id = 'id';
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
private static Array = array (
  'id' => 0,
  'account_id' => 1,
  'firstname' => 2,
  'lastname' => 3,
  'firstkana' => 4,
  'lastkana' => 5,
  'postzip' => 6,
  'birth' => 7,
  'face' => 8,
  'address' => 9,
  'sex' => 10,
  'type' => 11,
);
###active_define###
}