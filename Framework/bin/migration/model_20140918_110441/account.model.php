<?php
/**
 * account.model.php
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
 * account_model
 * 
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class account_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','account','password','salt','permission','status','token','facebook_id','register_dt','update_dt'
    );
    /**
    * カラム定義
    * @api
    * @var array
    * @link
    */
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'account' => '`account` varchar(40) NOT NULL',
  'password' => '`password` char(64) NOT NULL',
  'salt' => '`salt` varchar(30) NOT NULL',
  'permission' => '`permission` varchar(20) NOT NULL',
  'status' => '`status` enum(\'valid\',\'invalid\',\'pend\') NOT NULL Default \'valid\'',
  'token' => '`token` varchar(64) NOT NULL',
  'facebook_id' => '`facebook_id` varchar(64) NULL',
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
  'account' => 'UNIQUE KEY `account` (`account`,`permission`,`token`,`facebook_id`)',
  'update_dt' => ' KEY `update_dt` (`update_dt`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`account`' => 'id');
    ##indexes##
	/**
	 * 結合情報
	 * @api
	 * @var array
	 * @link
	 */
	public $relation = array();

	/**
	 * アカウントログイン時にタイムスタンプを更新(オプション)
	 * @api
	 * @return
	 * @link
	 */
	public function app_setup() {
		$this->query("update account set update_dt = ? where id = ?", array($_SERVER["REQUEST_TIME"], App::helper("auth")->id));
		App::helper("auth")->set("update_dt", $_SERVER["REQUEST_TIME"]);
		return true;
	}

	/**
	 * facebook Oauth 認証api
	 * @api
	 * @param Closure $logined システム上facebookアカウントを見つかった時処理(一般的にはログイン処理)
	 * @param Closure $register システム上facebookアカウントを見つからなかった時処理(一般的には登録処理)
	 * @return
	 * @link
	 */
	public function facebook_login($logined = null, $register = null) {
		App::helper("auth")->facebook_login(function($facebook_id, $facebook_profile, $auth) use($logined, $register) {
				$account = $this->find_by_facebook_id($facebook_id);
				if($account) {
					$auth->set_user($account->to_array());
					call_user_func($logined, $account, $facebook_profile);
				} else {
					call_user_func($register, $facebook_profile);
				}
			});
	}

	/**
	 * facebook Oauth 認証api
	 * @api
	 * @param String $callback_url twitterに渡すコールバックurl({@link https://dev.twitter.com/oauth twitter oauth api仕様})
	 * @param Closure $logined システム上twitterアカウントを見つかった時処理(一般的にはログイン処理)
	 * @param Closure $register システム上twitterアカウントを見つからなかった時処理(一般的には登録処理)
	 * @return
	 * @link
	 */
	public function twitter_login($callback_url = null, $logined = null, $register = null) {
		App::helper("twitter")->login($callback_url, function($twitter_connect) use($logined, $register) {
				$twitter  = $twitter_connect->get('account/verify_credentials');
				$account = false;
				if(isset($twitter->id_str)) {
					$account = $this->find_by_twitter_id($twitter->id_str);
				}
				if($account) {
					App::helper("auth")->set_user($account->to_array());
					if(is_callable($logined)) {
						call_user_func($logined, $account, $twitter);
					}
				} else {
					if(is_callable($register)) {
						call_user_func($register, $twitter);
					}
				}
			});
	}
}

/**
 * account_active_record
 * 
 * accountデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class account_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
private static account = 'account';
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
  'account' => 1,
  'password' => 2,
  'salt' => 3,
  'permission' => 4,
  'status' => 5,
  'token' => 6,
  'facebook_id' => 7,
  'register_dt' => 8,
  'update_dt' => 9,
);
###active_define###
}