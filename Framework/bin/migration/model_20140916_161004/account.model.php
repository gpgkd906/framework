<?php
class account_model extends model_core {
	##columns##
    public $columns = array(
        'id','account','password','salt','permission','status','token','facebook_id','register_dt','update_dt'
    );
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
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'account' => 'UNIQUE KEY `account` (`account`,`permission`,`token`,`facebook_id`)',
  'update_dt' => ' KEY `update_dt` (`update_dt`)',
);
              public $primary_keys = array('`account`' => 'id');
    ##indexes##
	##relation##
	public $has_many = array();
	public $belong_to = array();
    ##relation##
	public $relation = array();

	public function app_setup() {
		$this->query("update account set update_dt = ? where id = ?", array($_SERVER["REQUEST_TIME"], App::helper("auth")->id));
		App::helper("auth")->set("update_dt", $_SERVER["REQUEST_TIME"]);
		return true;
	}

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