<?php
class place_types_model extends model_core {
	##columns##
    public $columns = array(
        'id','place_id','wheelchair','autowheelchair','walker','babycar','pregnant','vision','hear','ostomate','other'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'wheelchair' => '`wheelchair` int(11) NOT NULL',
  'autowheelchair' => '`autowheelchair` int(11) NOT NULL',
  'walker' => '`walker` int(11) NOT NULL',
  'babycar' => '`babycar` int(11) NOT NULL',
  'pregnant' => '`pregnant` int(11) NOT NULL',
  'vision' => '`vision` int(11) NOT NULL',
  'hear' => '`hear` int(11) NOT NULL',
  'ostomate' => '`ostomate` int(11) NOT NULL',
  'other' => '`other` int(11) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'place_id' => 'UNIQUE KEY `place_id` (`place_id`)',
);
              public $primary_keys = array('`place_types`' => 'id');
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
		$record->wheelchair = 0;
		$record->autowheelchair = 0;
		$record->walker = 0;
		$record->babycar = 0;
		$record->pregnant = 0;
		$record->vision = 0;
		$record->hear = 0;
		$record->ostomate = 0;
		$record->other = 0;
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
    public function increase_type_by_place_id($place_id, $type) {
		$sql = array("update place_types set");
		$set = array();
		if(in_array($type, $this->columns)) {
			$set[] = "{$type}={$type}+1";				
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
    public function decrease_type_by_place_id($place_id, $type) {
		$sql = array("update place_types set");
		$set = array();
		if(in_array($type, $this->columns)) {
			$set[] = "{$type}={$type}-1";				
		}		
		$sql[] = join(",", $set);
		$sql[] = "where place_id=?";
		$this->query(join(" ", $sql), array($place_id));
    }

}