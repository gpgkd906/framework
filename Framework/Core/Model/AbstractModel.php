<?php

namespace Framework\Core\Model;

use FrameWork\Core\Interfaces\ModelInterface;
use Framework\Config\ConfigModel;
use PDO;

abstract class AbstractModel implements ModelInterface
{
    const FETCH_ASSOC = 2;
    
    static private $sqlBuilder = "Framework\Core\Model\SqlBuiler\Mysql";
    static private $connection = null;
    static private $config = null;
    static private $database = "mysql";
    static private $charset = "utf8";
    
    private $Record = "Framework\Core\Model\AbstractRecord";
    private $models = [];
    private $conditions = [];
    
    static public function getConnection()
    {
        if(self::$config === null) {
            self::$config = ConfigModel::getConfigModel([
                "scope" => ConfigModel::Model,
                "property" => ConfigModel::READONLY
            ]);
        }
        if(self::$connection === null) {
            $db = self::$config->getConfig("database", self::$database);
            $host = self::$config->getConfig("host");
            $user = self::$config->getConfig("user");
            $pass = self::$config->getConfig("password");
            $dbname = self::$config->getConfig("dbname");
            $charset = self::$config->getConfig("charset", self::$charset);
            $connectStatement = sprintf("%s:host=%s;dbname=%s;charset=%s", $db, $host, $dbname, $charset);
            self::$connection = new PDO($connectStatement, $user, $pass);
        }
        return self::$connection;
    }

    static public function getSingleton() {
        $modelName = get_called_class();
        if(!isset(self::$models[$modelName])) {
            self::$models[$modelName] = new $modelName();
        }
        return self::$models[$modelName];
    }

    private function __construct()
    {
        
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
                $tmp[] = {static::$sqlBuilder}::quete($selectItem);
            }
            $select = join(',', $tmp);
        }
        $selectSql = {static::$sqlBuilder}::getSelectQuery($parts);
        return $this->query($selectSql, $params);
    }

    public function update()
    {
        $parts = $this->collectParts();
        $params = $this->collectParams();
        $updateSql = {static::$sqlBuilder}::getUpdateQuery($parts);
        return $this->query($updateSql, $params);
    }
    
    public function delete()
    {
        $parts = $this->collectParts();
        $params = $this->collectParams();
        $deleteSql = {static::$sqlBuilder}::getDeleteQuery($parts);
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
        $RecordClass = $this->Record;
        return new $RecordClass;
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

}