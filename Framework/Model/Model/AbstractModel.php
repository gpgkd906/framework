<?php

namespace Framework\Model\Model;

use Framework\Model\Model\QueryExecutor;
use Exception;

abstract class AbstractModel implements ModelInterface
{
    use \Framework\Application\SingletonTrait;
    
    public function findBy($condition)
    {
        
    }

    public function find($id)
    {
        $result = false;
        if(is_numeric($id)) {
            $record = static::RECORD;
            $record = new $record($id);
            if($record->isValid()) {
                $result = $record;
            }
        }
        return $result;
    }
    
    public function getAll()
    {
        $record = static::RECORD;
        $record = new $record(1);
        var_dump($record);
        //QueryExecutor::query(null, null);
    }

    public function query($query, $param, $option = null)
    {
        $result = QueryExecutor::query($query, $param);
        return $result;
    }
}
