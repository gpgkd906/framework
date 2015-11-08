<?php

namespace Framework\Model\Model;

use Framework\Model\Model\QueryExecutor;
use Framework\Model\Model\SqlBuilder;
use Framework\Model\Model\AbstractRecord;
use Exception;

abstract class AbstractModel implements ModelInterface
{
    use \Framework\Application\SingletonTrait;

    /**
     *
     * @api
     * @var mixed $recordInfo 
     * @access private
     * @link
     */
    private $recordInfo = null;

    /**
     * 
     * @api
     * @param mixed $recordInfo
     * @return mixed $recordInfo
     * @link
     */
    public function setRecordInfo ($recordInfo)
    {
        return $this->recordInfo = $recordInfo;
    }

    /**
     * 
     * @api
     * @return mixed $recordInfo
     * @link
     */
    public function getRecordInfo ()
    {
        return $this->recordInfo;
    }

    
    public function __construct()
    {
        $record = static::RECORD;
        $record::setModel($this);
    }
    
    public function findBy($condition)
    {
        $sqlBuilder = SqlBuilder::createSqlBuilder();
        $sqlBuilder->select(static::RECORD)
                   ->from(static::RECORD);
        $recordInfo = $this->getRecordInfo();
        $propertyMap = $recordInfo[AbstractRecord::PROPERTY_MAP];
        $where = [];
        $param = [];
        foreach($condition as $property => $value) {
            if(isset($propertyMap[$property])) {
                $column = $propertyMap[$property];
                $bindKey = ':' . $column;
                if(is_array($value)) {
                    $sub = [];
                    foreach($value as $idx => $val) {
                        $param[$bindKey . $idx] = $val;
                        $sub[] = $bindKey . $idx;
                    }
                    $where[] = $propertyMap[$property] . ' in (' . join(', ', $sub) . ')';
                } else {
                    $param[$bindKey] = $value;
                    $where[] = $propertyMap[$property] . ' = ' . $bindKey;
                }
            }
        }
        $where = join(' AND ', $where);
        $sqlBuilder->where($where)
                   ->setParameter($param);        
        return $sqlBuilder->getResult();
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
    
    public function query($query, $param, $option = null)
    {
        $result = QueryExecutor::query($query, $param);
        return $result;
    }
}
