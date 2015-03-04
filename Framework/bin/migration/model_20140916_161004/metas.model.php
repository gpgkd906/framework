<?php
class metas_model extends model_core {
	##columns##
    public $columns = array(
        'id','meta_key','meta_value'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'meta_key' => '`meta_key` varchar(255) NOT NULL',
  'meta_value' => '`meta_value` longtext NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'meta_key' => 'UNIQUE KEY `meta_key` (`meta_key`)',
);
              public $primary_keys = array('`metas`' => 'id');
    ##indexes##
	##relation##
	public $has_many = array();
	public $belong_to = array();
    ##relation##
	public $relation = array();

	public function read($key) {
	}
	
	public function write($key, $value) {
	}
}