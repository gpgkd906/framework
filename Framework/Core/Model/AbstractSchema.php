<?php

namespace Framework\Core\Model;

use Framework\Core\Interfaces\Model\SchemaInterface;
use Exception;

abstract class AbstractSchema implements SchemaInterface
{
    const ERROR_CHECK_COLUMN = "error: check_column";
    
    protected $name = null;

    protected $columns = [];
    
    protected $indexs = [];

    protected $foreignKeys = [];

    protected $primaryKey = null;
    
    public function getColumns()
    {
        return $this->columns;
    }

    public function getColumn($key)
    {
        if(isset($this->columns[$key])) {
            return $this->columns[$key];
        }
    }

    public function hasColumn($key)
    {
        return isset($this->columns[$key]);
    }

    public function checkColumn($key, $msg = null)
    {
        if(!$this->hasColumn($key)) {
            if($msg == null) {
                $msg = self::ERROR_CHECK_COLUMN;
            }
            throw new Exception($msg);
        }
    }

    public function getIndexs()
    {
        return $this->indexs;
    }
    
    public function getIndex($key)
    {
        if(isset($this->indexs[$key])) {
            return $this->indexs[$key];
        }
    }
    
    public function getForeignKeys()
    {
        return $this->foreignKeys;
    }

    public function getForeignKey($key)
    {
        if(isset($this->foreignKeys[$key])) {
            return $this->foreignKeys[$key];
        }
    }
    
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function getName()
    {
        return $this->name;
    }
}
