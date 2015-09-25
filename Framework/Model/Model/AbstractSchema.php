<?php

namespace Framework\Model\Model;

use Exception;

abstract class AbstractSchema implements SchemaInterface
{
    const ERROR_CHECK_COLUMN = "error: check_column, has no column [%s] in table [%s]";
    const ERROR_INVALID_TIMESTAMP_KEY = "error: INVALID_TIMESTAMP_KEY [%s] in table [%s]";
    const ERROR_EMPTY_NAME = "error: undefined schema-name";

    const TIMESTAMP_DATE = "timestamp_date";
    const TIMESTAMP_TIME = "timestamp_time";

    protected $name = null;
    private $table = null;
    
    protected $columns = [];
    protected $timestamp = [
        "createDate" => "register_date",
        "createTime" => "register_time",
        "updateDate" => "update_date",
        "updateTime" => "update_time",
    ];

    private $timestampFlag = [
        "createDate" => null,
        "createTime" => null,
        "updateDate" => null,
        "updateTime" => null,
    ];
    
    private $flipColumns = null;
    private $formatColumns = null;
    private $objectKeys = null;
    private $nativeKeys = null;
    
    protected $indexs = [];
    protected $relations = [];

    private $objectPrimaryKey = null;
    protected $primaryKey = null;
    
    public function getObjectKeys()
    {
        if($this->objectKeys === null) {
            $this->objectKeys = array_keys($this->getColumns());
        }
        return $this->objectKeys;
    }

    public function getNativeKeys()
    {
        if($this->nativeKeys === null) {
            $this->nativeKeys = array_values($this->getColumns());
        }
        return $this->nativeKeys;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getFlipColumns()
    {
        if($this->flipColumns === null) {
            $this->flipColumns = array_flip($this->getColumns());
        }
        return $this->flipColumns;
    }

    public function getColumn($key)
    {
        if(isset($this->columns[$key])) {
            return $this->columns[$key];
        }
    }

    public function getFormatColumns()
    {
        if($this->formatColumns === null) {
            foreach($this->columns as $key => $column) {
                $this->formatColumns[$key] = $this->quote($this->name) . "." . $this->quote($column);                
            }
        }
        return $this->formatColumns;
    }
    
    public function getFormatColumn($key)
    {
        if(isset($this->columns[$key])) {
            return $this->getFormatColumns()[$key];
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
                throw new Exception(sprintf(self::ERROR_CHECK_COLUMN, $key, $this->getName()));
            } else {
                throw new Exception($msg);
            }
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
    
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function getObjectPrimaryKey()
    {
        if($this->objectPrimaryKey === null) {
            $this->objectPrimaryKey = array_search($this->primaryKey, $this->columns);
        }
        return $this->objectPrimaryKey;
    }

    public function getName()
    {
        if(empty($this->name)) {
            throw new Exception(self::ERROR_EMPTY_NAME);
        }
        return $this->name;
    }

	public function getTable(){
        if($this->table == null) {
            $this->table = $this->quote($this->name);
        }
        return $this->table;
	}

    public function quote($string)
    {
        return '`' . $string . '`';
    }

    public function hasTimeStamp($key)
    {
        $timestamp = $this->timestamp;
        if(!array_key_exists($key, $timestamp)) {
            throw new Exception(sprintf(self::ERROR_INVALID_TIMESTAMP_KEY, $key, $this->getName()));
        }
        $timestampFlag = $this->timestampFlag[$key];
        if($this->timestampFlag[$key] === null) {
            //check if there is a timestamp column
            $objectKey = $timestamp[$key];
            $this->timestampFlag[$key] = $this->hasColumn($objectKey);
        }
        return $this->timestampFlag[$key];
    }

     public function getObjectTimeStamp($key)
     {
         $timestamp = $this->timestamp;
         if(!array_key_exists($key, $timestamp)) {
             throw new Exception(sprintf(self::ERROR_INVALID_TIMESTAMP_KEY, $key, $this->getName()));
         }
         return $timestamp[$key];
     }

     public function getRelations()
     {
         return $this->relations;
     }
}
