<?php

namespace Framework\Core\Model;

use Framework\Core\Interfaces\Model\ModelInterface;
use Framework\Core\Interfaces\Model\SchemaInterface;
use Framework\Config\ConfigModel;
use PDO;
use Exception;

abstract class AbstractModel implements ModelInterface
{
    const ERROR_UNDEFINED_SCHEMA = "error: undefined schema in model [%s]";
    const ERROR_UNDEFINED_RECORD = "error: undefined record in model [%s]";
    
    const FETCH_ASSOC = 2;
    const DEFAULT_SQLBUILDER = "Framework\Core\Model\SqlBuilder\MySql";
    static private $connection = null;
    private $Schema = null;
    private $schemaLabel = null;
    private $recordLabel = null;
    private $sqlBuilder = null;
    private $stmt = null;

    static private $modelConfig = [
        "database" => "mysql",
        "charset"  => "utf8",
    ];
    
    protected $config = [
        "Schema" => null,
        "Record" => null,
    ];
    static private $models = [];

    static protected function getModelConfig()
    {
        if(is_array(self::$modelConfig)) {
            $modelConfig = self::$modelConfig;
            self::$modelConfig = ConfigModel::getConfigModel([
                "scope" => ConfigModel::MODEL,
                "property" => ConfigModel::READONLY
            ]);
            foreach($modelConfig as $key => $default) {
                if(!self::$modelConfig->getConfig($key)) {
                    self::$modelConfig->setConfig($key, $default);
                }
            }
        }
        return self::$modelConfig;
    }
    
    
    static public function getConnection()
    {
        if(self::$connection === null) {
            $modelConfig = self::getModelConfig();
            $db = $modelConfig->getConfig("database", self::$database);
            $host = $modelConfig->getConfig("host");
            $user = $modelConfig->getConfig("user");
            $pass = $modelConfig->getConfig("password");
            $dbname = $modelConfig->getConfig("dbname");
            $charset = $modelConfig->getConfig("charset", self::$charset);
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
        $modelName = get_called_class();
        if(empty($this->config["Schema"])) {            
            throw new Exception(sprintf(self::ERROR_UNDEFINED_SCHEMA, $modelName));
        }
        if(empty($this->config["Record"])) {
            throw new Exception(sprintf(self::ERROR_UNDEFINED_RECORD, $modelName));
        }
        $this->schemaLabel = $this->config["Schema"];
        $this->recordLabel = $this->config["Record"];
        $this->getSqlBuilder();
    }

    public function find()
    {
        $sqlBuilder = $this->getSqlBuilder();
        
    }
    
    public function order()
    {
        $sqlBuilder = $this->getSqlBuilder();
        
    }
    
    public function group()
    {
        $sqlBuilder = $this->getSqlBuilder();
        
    }
    
    public function join()
    {
        $sqlBuilder = $this->getSqlBuilder();
        
    }
    
    public function select($select = null)
    {
        $sqlBuilder = $this->getSqlBuilder();
    }

    public function update()
    {
    }
    
    public function delete()
    {
    }

    public function query($sql, $params)
    {
        $this->stmt = $this->getConnection()->prepare($sql);
    }

    public function getSqlBuilder()
    {
        if($this->sqlBuilder === null) {
            $Config = self::getModelConfig();
            $sqlBuilderLabel = $Config->getConfig("sqlBuilder", self::DEFAULT_SQLBUILDER);
            $this->sqlBuilder = new $sqlBuilderLabel;
            $this->sqlBuilder->setSchema($this->getSchema());
        }
        return $this->sqlBuilder;
    }

    public function getSchema()
    {
        if($this->Schema === null) {
            $schemaLabel = $this->schemaLabel;
            $this->Schema = new $schemaLabel;
        }
        return $this->Schema;
    }

    public function create($data)
    {
        $record = $this->newRecord();
        $record->assign($data);
        $record->save();
    }

    public function newRecord()
    {
        $recordClass = $this->recordLabel;
        return new $recordClass;
    }
        
    public function get()
    {
        if($this->stmt == null) {
            $this->select();
        }
        return $this->fetch();
    }

    public function getAll()
    {
        if($this->stmt == null) {
            $this->select();
        }
        return $this->fetchAll();
    }

    public function getArray()
    {
        if($this->stmt == null) {
            $this->select();
        }
        return $this->fetchArray();
    }

    public function getAllArray()
    {
        if($this->stmt == null) {
            $this->select();
        }
        return $this->fetchAllArray();
    }

    public function fetch()
    {
        if($this->stmt) {
            if($data = $this->stmt->fetch(self::FETCH_ASSOC)) {
                $record = $this->newRecord();
                $record->assign($data);
                return $record;
            }
            $this->stmt = null;
        }
        return false;
    }

    public function fetchAll()
    {
        if($this->stmt) {
            $records = [];
            while($record = $this->fetch()) {
                $records[] = $record;
            }
            $this->stmt = null;
            return $records;
        }
    }

    public function each($callBack)
    {
        while($record = $this->fetch()) {
            call_user_func($callBack, $record);
        }
    }

    public function fetchArray()
    {
        if($this->stmt) {
            if($data = $this->stmt->fetch(self::FETCH_ASSOC)) {
                return $data;
            }
            $this->stmt = null;
        }
    }

    public function fetchAllArray()
    {
        if($this->stmt) {
            $data = $this->stmt->fetchAll(self::FETCH_ASSOC);
            $this->stmt = null;
            return $data;
        }
    }

    public function eachArray($callBack)
    {
        while($row = $this->fetchArray()) {
            call_user_func($callBack, $row);
        }
    }
}