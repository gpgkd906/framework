<?php

namespace Framework\Core\Model;

use Framework\Core\Interfaces\Model\ModelInterface;
use Framework\Config\ConfigModel;
use PDO;
use Exception;

abstract class AbstractModel implements ModelInterface
{
    const ERROR_UNDEFINED_SCHEMA = "error: undefined schema in model [%s]";
    const ERROR_UNDEFINED_RECORD = "error: undefined record in model [%s]";

    const FETCH_ASSOC = 2;
    
    static private $sqlBuilder = "Framework\Core\Model\SqlBuiler\Mysql";
    static private $connection = null;
    private $stmt;

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
                "scope" => ConfigModel::Model,
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
    
    public function select($select = null)
    {
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
        
    public function fetch()
    {
        if($this->stmt) {
            if($data = $this->stmt->fetch(self::FETCH_ASSOC)) {
                $record = $this->newRecord();
                $record->assign($data);
                return $record;
            }
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
            return $records;
        }
    }

    public function get()
    {
        $this->select();
        return $this->fetch();
    }

    public function getAll()
    {
        $this->select();
        return $this->fetchAll();
    }

    public function getArray()
    {
        $this->select();
        return $this->fetchArray();
    }

    public function getAllArray()
    {
        $this->select();
        return $this->fetchAllArray();
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
            $data = $this->stmt->fetch(self::FETCH_ASSOC);
            return $data;
        }
    }

    public function fetchAllArray()
    {
        if($this->stmt) {
            $data = $this->stmt->fetchAll(self::FETCH_ASSOC);
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