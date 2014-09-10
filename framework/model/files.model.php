<?php
class files_model extends model_core {
	##columns##
    public $columns = array(
        'id','file','filename','size','mime','path','link','register_dt','update_dt'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'file' => '`file` varchar(255) NOT NULL',
  'filename' => '`filename` varchar(255) NOT NULL',
  'size' => '`size` int(11) NOT NULL',
  'mime' => '`mime` varchar(255) NOT NULL',
  'path' => '`path` varchar(255) NOT NULL',
  'link' => '`link` varchar(255) NOT NULL',
  'register_dt' => '`register_dt` bigint(20) NOT NULL',
  'update_dt' => '`update_dt` bigint(20) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'mime' => ' KEY `mime` (`mime`)',
);
              public $primary_keys = array('`files`' => 'id');
    ##indexes##
	##relation##
	public $has_many = array();
	public $belong_to = array();
    ##relation##
	public $relation = array();
}