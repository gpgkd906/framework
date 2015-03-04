<?php
class file_votes_model extends model_core {
##columns##
    public $columns = array(
        'id','file_id','voter'
    );
    public $alter_columns = array (
  'id' => '`id` int(11) NOT NULL  AUTO_INCREMENT',
  'file_id' => '`file_id` int(11) NOT NULL',
  'voter' => '`voter` int(11) NOT NULL',
);
    ##columns##
##indexes##
    public $alter_indexes = array (
  'PRIMARY' => 'PRIMARY KEY  (`id`)',
  'file_id' => 'UNIQUE KEY `file_id` (`file_id`,`voter`)',
);
              public $primary_keys = array('`file_votes`' => 'id');
    ##indexes##
public $relation = array();
}