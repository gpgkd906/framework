<?php
class notification_model extends model_core {
	##columns##
    public $columns = array(
        'id','account_id','device_id','device_type','endpoint'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'account_id' => '`account_id` int(11) NOT NULL',
  'device_id' => '`device_id` varchar(255) NOT NULL',
  'device_type' => '`device_type` varchar(255) NOT NULL',
  'endpoint' => '`endpoint` longtext NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'notification' => 'UNIQUE KEY `notification` (`device_id`,`device_type`,`account_id`)',
);
              public $primary_keys = array('`notification`' => 'id');
    ##indexes##
	public $relation = array();

	/* 
	   同じアカウントは複数のデバイスをバインドすることができる、同じデバイスは一つのアカウントにバインドされるしかできません
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