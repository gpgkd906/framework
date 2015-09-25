<?php

namespace Framework\Model\Model;

use Framework\Event\Event\EventInterface;
use Framework\Config\ConfigModel;
use Exception;

abstract class AbstractModel implements ModelInterface, EventInterface
{
    use \Framework\Event\Event\EventTrait;
    use \Framework\Application\SingletonTrait;
    
    const ERROR_UNDEFINED_SCHEMA = "error: undefined schema in model [%s]";
    const ERROR_UNDEFINED_RECORD = "error: undefined record in model [%s]";
    const ERROR_INVALID_SCHEMA_FOR_JOIN = "error: INVALID_SCHEMA_FOR_JOIN";
    const ERROR_INVALID_COLUMN_FOR_JOIN = "error: invalid column [%s] for join schema [%s]";
    
    const FETCH_ASSOC = 2;
    const DEFAULT_SQLBUILDER = SqlBuilder\MySql::class;
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

    const TRIGGER_SELECT = "Select";
    const TRIGGER_UPDATE = "Update";
    const TRIGGER_INSERT = "Insert";
    const TRIGGER_DELETE = "Delete";
    
    static private $models = [];

        /**
     *
     * @api
     * @var mixed $serviceManager 
     * @access private
     * @link
     */
    private $serviceManager = null;

    /**
     * 
     * @api
     * @param mixed $serviceManager
     * @return mixed $serviceManager
     * @link
     */
    public function setServiceManager ($serviceManager)
    {
        return $this->serviceManager = $serviceManager;
    }

    /**
     * 
     * @api
     * @return mixed $serviceManager
     * @link
     */
    public function getServiceManager ()
    {
        return $this->serviceManager;
    }

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
    
    private function __construct()
    {
        $this->triggerEvent(self::TRIGGER_INIT);
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
        $this->triggerEvent(self::TRIGGER_INITED);
    }

    public function setValue($column, $value)
    {
        $this->getSqlBuilder()->set($column, $value);
    }

    public function putValues($data)
    {
        $sqlBuilder = $this->getSqlBuilder();
        foreach($data as $column => $value) {
            $sqlBuilder->set($column, $value);
        }
    }

    public function find($column, $bind = null, $opera = "=")
    {
        $this->getSqlBuilder()->find($column, $bind, $opera, "AND");
    }
    
    public function orFind($column, $bind = null, $opera = "=")
    {
        $this->getSqlBuilder()->find($column, $bind, $opera, "OR");
    }

    public function limit($l1, $l2 = null)
    {
        $this->getSqlBuilder()->limit($l1, $l2);
    }
    
    public function order($column, $order = "ASC")
    {
        $this->getSqlBuilder()->addOrder($column, $order);
    }
    
    public function group($column)
    {
        $this->getSqlBuilder()->addGroup($column);
    }
    
    public function join($Schema, $from, $to)
    {
        if($Schema instanceof ModelInterface) {
            $Schema = $Schema->getSchema();
        }
        if(!$Schema instanceof SchemaInterface) {
            throw new Exception(self::ERROR_INVALID_SCHEMA_FOR_JOIN);
        }
        if(!$this->getSchema()->hasColumn($from)) {
            throw new Exception(sprintf(self::ERROR_INVALID_COLUMN_FOR_JOIN, $from, $this->getSchema()->getName()));
        }
        if(!$Schema->hasColumn($to)) {
            throw new Exception(sprintf(self::ERROR_INVALID_COLUMN_FOR_JOIN, $to, $Schema->getName()));
        }
        $this->getSqlBuilder()->join($Schema, $from, $to);
    }
    
    public function select()
    {
        $this->triggerEvent(self::TRIGGER_SELECT);
        $sqlBuilder = $this->getSqlBuilder();
        $joinStack = $sqlBuilder->getJoin();
        if(empty($joinStack)) {
            $column = $this->getSchema()->getFormatColumns();
        } else {
            throw new Exception("ERROR: select_join is not implements yes");
        }
        $this->stmt = $this->getSqlBuilder()->select($column)->query();
        return $this;
    }

    public function insert()
    {
        $this->triggerEvent(self::TRIGGER_INSERT);
        $this->getSqlBuilder()->insert()->query();
    }

    public function update()
    {
        $this->triggerEvent(self::TRIGGER_UPDATE);
        return $this->getSqlBuilder()->update()->query();
    }
    
    public function delete()
    {
        $this->triggerEvent(self::TRIGGER_DELETE);
        return $this->getSqlBuilder()->delete()->query();
    }

    public function getSqlBuilder()
    {
        if($this->sqlBuilder === null) {
            $Config = self::getModelConfig();
            $sqlBuilderLabel = $Config->getConfig("sqlBuilder", self::DEFAULT_SQLBUILDER);
            $this->sqlBuilder = new $sqlBuilderLabel;
            $this->sqlBuilder->setSchema($this->getSchema());
            $this->sqlBuilder->setConnectionInfo($Config->getConfig("connection"));
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

    public function bindRecord($data)
    {
        $recordClass = $this->recordLabel;
        $tempStore = $recordClass::getFormat();
        $keyMap = $this->getSchema()->getColumns();
        $isDirtyRecord = false;
        foreach($tempStore as $key => $null) {
            $nativeColumn = $keyMap[$key];
            if(isset($data[$nativeColumn])) {
                $tempStore[$key] = $data[$nativeColumn];
            } else {
                $isDirtyRecord = true;
            }
        }
        $record = new $recordClass($isDirtyRecord, $tempStore);
        return $record;
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
                return $this->bindRecord($data);
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