<?php
/**
 * notification.model.php
 *
 *
 * myFramework : Origin Framework by Chen Han https://github.com/gpgkd906/framework
 *
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
 * notification_model
 * 
 * プッシュ通知用データベース
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class notification_model extends model_core {
	##columns##
    /**
    * カラム
    * @api
    * @var array
    * @link
    */
    public $columns = array(
        'id','account_id','device_id','device_type','endpoint'
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
  'device_id' => '`device_id` varchar(255) NOT NULL',
  'device_type' => '`device_type` varchar(255) NOT NULL',
  'endpoint' => '`endpoint` longtext NOT NULL',
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
  'notification' => 'UNIQUE KEY `notification` (`device_id`,`device_type`,`account_id`)',
);
    /**
    * プライマリーキー
    * @api
    * @var array
    * @link
    */
              public $primary_keys = array('`notification`' => 'id');
    ##indexes##
	/**
	 *
	 *
	 * @var array
	 * @link
	 */
	public $relation = array();

	/**
	 * デバイスとアカウントを紐付ける
	 *
	 * 同じアカウントは複数のデバイスをバインドすることができる
	 *
	 * 同じデバイスは一つのアカウントにバインドされるしかできません
	 * @param String $account_id アカウントid
	 * @param String $device_id デバイスid
	 * @param String $device_type デバイスタイプ
	 * @return
	 * @link
	 */
	public function bound($account_id, $device_id, $device_type) {
		$this->find("device_id", $device_id)->find("device_type", $device_type);
		if(!$record = $this->get()) {
			$record = $this->new_record();
			if($device_type === "GCM") {
				$record->assign(array(
						"account_id" => $account_id,
						"device_id" => $device_id,
						"device_type" => $device_type
				));
				$record->save();
			}
			$record->endpoint = App::helper("aws_sdk")->sns_create_endpoint($device_id, $device_type);
		}
		$record->assign(array(
				"account_id" => $account_id,
				"device_id" => $device_id,
				"device_type" => $device_type
		));
		$record->save();
	}

	/**
	 * プッシュ通知を送る
	 *
	 * @param String $account_id アカウントid
	 * @param String $messages 通知内容
	 * @return
	 * @link
	 */
	public function publish($account_id, $messages) {
		if(empty($messages)) {
			return false;
		}
		if(!is_array($messages)) {
			$messages = array($messages);
		}
		$endpoints = $this->find_all_by_account_id($account_id);
		$sns = App::helper("aws_sdk");
		foreach($endpoints as $record) {
			foreach($messages as $message) {
				$sns->sns_publish($record->device_id, $record->device_type, $record->endpoint, $message);
			}
		}
	}
}

/**
 * notification_active_record
 * 
 * notificationデータベースのアクティブレコード
 *
 * @author 2014 Chen Han 
 * @package framework.model
 * @link 
 */
class notification_active_record extends active_record_core {
	###active_define###
/**
*
* テーブル名
* @api
* @var 
* @link
*/
private static notification = 'notification';
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
  'device_id' => 2,
  'device_type' => 3,
  'endpoint' => 4,
);
###active_define###
}