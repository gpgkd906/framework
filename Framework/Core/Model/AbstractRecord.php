<?php

namespace Framework\Core\Model;

use Framework\Core\Interfaces\Model\RecordInterface;
use Exception;

abstract class AbstractRecord implements RecordInterface
{
    const ERROR_UNDEFINED_SCHEMA = "error: schema is undefined";
    
    static $model = null;

    static $scheme = null;
    
    private $store;
    
    private $extStore;

    private $updated = false;

    private $deleted = false;

    public function __construct($data)
    {
        if(empty(static::$scheme)) {
            throw new Exception(self::ERROR_UNDEFINED_SCHEMA);
        }
        $this->store = $data;
    }

    public function assign($data)
    {
        
    }
    
    public function set($col, $value)
    {
        if(isset($this->store[$col])) {
            if($value != $this->store[$col]) {
                $this->store[$col] = $value;
                $this->updated = true;
            }
        } else {
            $this->extStore[$col] = $value;
        }
    }

    public function get($col)
    {
        if(isset($this->store[$col])) {
            return $this->store[$col];
        } else if(isset($this->extStore[$col])) {
            return $this->extStore[$col];
        }
        return null;
    }

    public function save()
    {
        if($this->updated) {
            
        }
    }

    public function delete()
    {

    }
}
