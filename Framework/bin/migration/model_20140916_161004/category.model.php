<?php
class category_model extends model_core {
	##columns##
    public $columns = array(
        'id','name'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'name' => '`name` varchar(255) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'cname' => 'UNIQUE KEY `cname` (`name`)',
);
              public $primary_keys = array('`category`' => 'id');
    ##indexes##
	public $relation = array();



/**
 * @api map_category
 * @param {Array} names 
 * マッピングするカテゴリ名
 * @return
 */
    public function map_category ($names) {
		$tmp = $this->find_all_by_name($names, true);
		$ids = array_column($tmp, "id");
		$_names = array_column($tmp, "name");
		$diffs = array_diff($names, $_names);
		foreach($diffs as $diff) {
			$ids[] = $this->create_record(array("name" => $diff))->id;
		}
		return $ids;
    }
	

}