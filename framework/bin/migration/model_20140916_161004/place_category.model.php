<?php
class place_category_model extends model_core {
	##columns##
    public $columns = array(
        'id','place_id','category'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'place_id' => '`place_id` varchar(128) NOT NULL',
  'category' => '`category` int(11) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'place_id' => 'UNIQUE KEY `place_id` (`place_id`,`category`)',
);
              public $primary_keys = array('`place_category`' => 'id');
    ##indexes##
	public $relation = array();


    /**
	 * @api bind_category
	 * @param {String} place_id 
	 * Google Map用PlaceId
	 * @param {Array} cate_ids 
	 * カテゴリid(配列・複数)
	 * @return
	 */
    public function bind_category ($place_id, $cate_ids) {
		foreach($cate_ids as $category) {
			$this->create_record(array("place_id" => $place_id, "category" => $category));
		}
    }

}

class place_category_active_record extends active_record_core {

}