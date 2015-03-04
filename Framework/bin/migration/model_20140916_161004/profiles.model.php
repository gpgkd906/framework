<?php
class profiles_model extends model_core {
	##columns##
    public $columns = array(
        'id','account_id','firstname','lastname','firstkana','lastkana','postzip','birth','face','address','sex','type'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'account_id' => '`account_id` int(11) NOT NULL',
  'firstname' => '`firstname` varchar(255) NOT NULL',
  'lastname' => '`lastname` varchar(255) NOT NULL',
  'firstkana' => '`firstkana` varchar(255) NOT NULL',
  'lastkana' => '`lastkana` varchar(255) NOT NULL',
  'postzip' => '`postzip` varchar(255) NOT NULL',
  'birth' => '`birth` date NOT NULL',
  'face' => '`face` longblob NOT NULL',
  'address' => '`address` varchar(255) NOT NULL',
  'sex' => '`sex` enum(\'男性\',\'女性\',\'秘密\') NOT NULL Default \'秘密\'',
  'type' => '`type` varchar(255) NOT NULL',
);
    ##columns##
	##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'account_id' => 'UNIQUE KEY `account_id` (`account_id`)',
);
              public $primary_keys = array('`profiles`' => 'id');
    ##indexes##
	##relation##
	public $has_many = array();
	public $belong_to = array();
    ##relation##
	public $relation = array();

	public $acts_as = array("api");
}