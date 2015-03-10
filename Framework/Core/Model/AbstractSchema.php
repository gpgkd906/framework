<?php

namespace Framework\Core\Model;

use Framework\Core\Interfaces\Model\SchemaInterface;
use Exception;

abstract class AbstractSchema implements SchemaInterface
{
    static private $name = null;

    static private $columns = [];
    
    static private $indexs = [];

    static private $foreignKey = [];

    static private $primaryKey = null;

    static public function getColumns($key = null)
    {
        if(isset(static::$columns[$key])) {
            return static::$columns[$key];
        } else {
            return static::$columns;
        }
    }

    static public function getIndex($key = null)
    {
        if(isset(static::$indexs[$key])) {
            return static::$indexs[$key];
        } else {
            return static::$indexs;
        }
    }
    
    static public function getForeignKey($key = null)
    {
        if(isset(static::$foreignKey[$key])) {
            return static::$foreignKey[$key];
        } else {
            return static::$foreignKey;
        }
    }
    
    public function getPrimaryKey()
    {
        return static::$primaryKey;
    }

    public function getName()
    {
        return static::$name;
    }
}
