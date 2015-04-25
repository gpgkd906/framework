<?php

namespace Framework\Core\Model;

use FrameWork\Core\Interfaces\ModelInterface;

abstract class AbstractModel implements ModelInterface
{
    const FETCH_ASSOC = 2;
    
    static private $sqlBuilder = "Framework\Core\Model\SqlBuiler\Mysql";
    static private $record = "Framework\Core\Model\AbstractRecord";

    private $models = [];

    private $conditions = [];
    
    static public function getSingleton() {
        $modelName = get_called_class();
        if(!isset(self::$models[$modelName])) {
            self::$models[$modelName] = new $modelName();
        }
        return self::$models[$modelName];
    }
    
    public function find()
    {
        
    }
    
    public function order()
    {

    }

    public function group()
    {

    }

    public function join()
    {

    }

    public function select($select)
    {
        $parts = $this->collectParts();
        $params = $this->collectParams();
        if(is_array($select)) {
            $tmp = [];
            foreach($select as $selectItem) {
                $tmp[] = static::$sqlBuilder::quete($selectItem);
            }
            $select = join(',', $tmp);
        }
        $selectSql = static::$sqlBuilder::getSelectQuery($parts);
        return $this->query($selectSql, $params);
    }

    public function update()
    {
        $parts = $this->collectParts();
        $params = $this->collectParams();
        $updateSql = static::$sqlBuilder::getUpdateQuery($parts);
        return $this->query($updateSql, $params);
    }
    
    public function delete()
    {
        $parts = $this->collectParts();
        $params = $this->collectParams();
        $deleteSql = static::$sqlBuilder::getDeleteQuery($parts);
        return $this->query($updateSql, $params);
    }

    public function create($data)
    {
        $record = $this->newRecord();
        $record->assign($data);
        $record->save();
    }

    public function newRecord()
    {
        return new static::$record;
    }

    public function getRecord()
    {
        $data = $this->stmt->fetch(self::FETCH_ASSOC);
        $record = $this->newRecord();
        $record->assign($data);
        return $record;
    }

    public function getAllRecord()
    {
        
    }

    public function getArray()
    {
        $data = $this->stmt->fetch(self::FETCH_ASSOC);
        return $data;        
    }

    public function getAllArray()
    {
        $data = $this->stmt->fetchAll(self::FETCH_ASSOC);
        return $data;        
    }
    
    public function setFilters($filters)
    {
        
    }
    
    public function getFilters()
    {
        
    }

    public function skipFilter()
    {
        
    }

    public function setRelations()
    {
        
    }

    public function getRelations()
    {
        
    }

    public function skipRelation()
    {
        
    }

    public function query($sql, $params)
    {
        $this->stmt = $this->getConnection()->prepare($sql);
        
    }

    public function getConnection()
    {
        
    }
}