<?php
class interesting_model extends model_core {
	##columns##
    public $columns = array(
        'id','review_id','type','sender_id','register_dt','update_dt'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'review_id' => '`review_id` int(11) NOT NULL',
  'type' => '`type` enum(\'good\',\'bad\') NOT NULL Default \'good\'',
  'sender_id' => '`sender_id` int(11) NOT NULL',
  'register_dt' => '`register_dt` bigint(20) NOT NULL',
  'update_dt' => '`update_dt` bigint(20) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'review_id' => 'UNIQUE KEY `review_id` (`review_id`,`sender_id`,`type`)',
);
              public $primary_keys = array('`interesting`' => 'id');
    ##indexes##
	public $relation = array();


/**
 * 
 * @param string 
 * @param integer 
 * @param array 
 * @param resource 
 * @param object 
 * @param mix 
 * @return
 */
    public function append($interesting) {
		$record = $this->new_record();
		$record->assign($interesting);
		return $record->save();
    }


/**
 * 
 * @param string 
 * @param integer 
 * @param array 
 * @param resource 
 * @param object 
 * @param mix 
 * @return
 */
    public function remove($interesting) {
		$this->find("review_id", $interesting["review_id"]);
		$this->find("sender_id", $interesting["sender_id"]);
		$this->find("type", $interesting["type"]);
		if($record = $this->get()) {
			$record->delete();
			return true;
		}
		return false;
    }
}