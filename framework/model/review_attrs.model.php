<?php
class review_attrs_model extends model_core {
	##columns##
    public $columns = array(
        'id','review_id','toilet','space','flat','elevator','parking','quiet','ostomate','baby','socket','smoking'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'review_id' => '`review_id` int(11) NOT NULL',
  'toilet' => '`toilet` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'space' => '`space` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'flat' => '`flat` enum(\'yes\',\'no\') NULL Default \'no\'',
  'elevator' => '`elevator` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'parking' => '`parking` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'quiet' => '`quiet` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'ostomate' => '`ostomate` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'baby' => '`baby` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'socket' => '`socket` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
  'smoking' => '`smoking` enum(\'yes\',\'no\') NOT NULL Default \'no\'',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'review_id' => 'UNIQUE KEY `review_id` (`review_id`,`toilet`,`space`,`flat`,`elevator`,`parking`,`quiet`,`ostomate`,`baby`,`socket`,`smoking`)',
);
              public $primary_keys = array('`review_attrs`' => 'id');
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
    public function append($review_id, $attrs) {
		$record = $this->new_record();
		foreach($this->columns as $col) {
			if(in_array($col, $attrs)) {
				$record->$col = "yes";
			} else {
				$record->$col = "no";
			}
		}
		$record->review_id = $review_id;
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
    public function remove($review_id) {
		if($record = $this->find_by_review_id($review_id)) {
			$record->delete();
		}
    }


}