<?php
class place_attrs_model extends model_core {
	##columns##
    public $columns = array(
        'id','place_id','toilet','space','flat','elevator','parking','quiet','ostomate','baby','socket','smoking'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'toilet' => '`toilet` int(11) NOT NULL',
  'space' => '`space` int(11) NOT NULL',
  'flat' => '`flat` int(11) NOT NULL',
  'elevator' => '`elevator` int(11) NOT NULL',
  'parking' => '`parking` int(11) NOT NULL',
  'quiet' => '`quiet` int(11) NOT NULL',
  'ostomate' => '`ostomate` int(11) NOT NULL',
  'baby' => '`baby` int(11) NOT NULL',
  'socket' => '`socket` int(11) NOT NULL',
  'smoking' => '`smoking` int(11) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'place_id' => 'UNIQUE KEY `place_id` (`place_id`)',
);
              public $primary_keys = array('`place_attrs`' => 'id');
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
    public function initialization($place_id) {
		$record = $this->new_record();
		$record->place_id = $place_id;
		$record->toilet = 0;
		$record->space = 0;
		$record->flat = 0;
		$record->elevator = 0;
		$record->parking = 0;
		$record->quiet = 0;
		$record->ostomate = 0;
		$record->baby = 0;
		$record->socket = 0;
		$record->smoking = 0;
		$record->save();
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
    public function increase_info_by_place_id($place_id, $info) {
		$sql = array("update place_attrs set");
		$set = array();
		foreach($this->columns as $col) {
			if(in_array($col, $info)) {
				$set[] = "{$col}={$col}+1";
			}
		}
		$sql[] = join(",", $set);
		$sql[] = "where place_id=?";
		$this->query(join(" ", $sql), array($place_id));
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
    public function decrease_info_by_place_id($place_id, $info) {
		unset($info["id"]);
		$sql = array("update place_attrs set");
		$set = array();
		foreach($this->columns as $col) {
			if(isset($info[$col]) && $info[$col] === "yes") {
				$set[] = "{$col}={$col}-1";
			}
		}
		$sql[] = join(",", $set);
		$sql[] = "where place_id=?";
		$this->query(join(" ", $sql), array($place_id));		
    }

}