<?php
class place_images_model extends model_core {
##columns##
    public $columns = array(
        'id','place_id','author','file_id','type','vote'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'author' => '`author` int(11) NOT NULL',
  'file_id' => '`file_id` int(11) NOT NULL',
  'type' => '`type` varchar(64) NOT NULL',
  'vote' => '`vote` int(11) NOT NULL',
);
    ##columns##
##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'place_id' => 'UNIQUE KEY `place_id` (`place_id`,`author`,`file_id`,`vote`)',
);
              public $primary_keys = array('`place_images`' => 'id');
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
    public function increase_vote($place_id, $file_id) {
		$this->query("update place_images set vote=vote+1 where place_id=? and file_id=?", array($place_id, $file_id));
		return $this->get_vote($place_id, $file_id);
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
    public function decrease_vote($place_id, $file_id) {
		$this->query("update place_images set vote=vote-1 where place_id=? and file_id=?", array($place_id, $file_id));
		return $this->get_vote($place_id, $file_id);
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
    public function get_vote($place_id, $file_id) {
		$record = $this->find("place_id", $place_id)->find("file_id", $file_id)->get_as_array();
		if($record["vote"] < 0) {
			$this->query("update place_images set vote=0 where place_id=? and file_id=?", array($place_id, $file_id));
			$record["vote"] = 0;
		}
		return $record["vote"];
    }
	
	
}