<?php

namespace Framework\Model\Model;

use Framework\Event\Event\EventInterface;
use Framework\Model\Model\ModelInterface;
use Framework\Model\Model\SqlBuilderInterface;
use Framework\Model\Model\Collection;
use Framework\Model\Model\AssiociateHelper;
use Framework\Model\Model\AssiociateHelper\AssiociateHelperInterface as Assiociate;
use ReflectionClass;
use Exception;

abstract class AbstractRecord implements RecordInterface, EventInterface
{
    use \Framework\Event\Event\EventTrait;
    
    const INSERT = 'Insert';
    const UPDATE = 'Update';
    const DELETE = 'Delete';
    const SELECT = 'Selete';
    //
    const TABLE        = 'Table';
    const COLUMN       = 'Column';
    const ID           = 'Id';
    const PRIMARY_KEY  = 'PrimaryKey';
    const ENTITY       = 'Entity';
    const PROPERTY     = 'Property';
    const QUERY        = 'Query';
    const NAME         = 'name';
    const PROPERTY_MAP = 'propertyMap';
    const COLUMN_MAP   = 'columnMap';
    const MODEL_CLASS  = 'modelClass';
    const PRIMARY_PROPERTY = 'primaryProperty';
    const SETTER       = 'setter';
    //
    const TRIGGER_PREINSERT  = 'preInsert';
    const TRIGGER_PREUPDATE  = 'preUpdate';
    const TRIGGER_PREDELETE  = 'preDelete';
    const TRIGGER_POSTINSERT = 'postInsert';
    const TRIGGER_POSTUPDATE = 'postUpdate';
    const TRIGGER_POSTDELETE = 'postDelete';
    
    static protected $info = [];
    static private $Model = null;
    static private $config = null;
    static private $sqlbuilder = null;
    private $isValid = true;
    private $queryInfo = null;    
    private $assiociateInitedFlag = false;
    
    public function __construct($primaryValueOrRaw = null)
    {
        $this->getQueryInfo();
        if($primaryValueOrRaw === null) {
            return false;
        }
        if(is_array($primaryValueOrRaw)) {
            $this->assign($primaryValueOrRaw);
        } else {
            $this->fetchRaw($primaryValueOrRaw);
        }
    }

    public function save()
    {
        if($this->isValid) {
            $recordInfo = self::getRecordInfo();
            $queryInfo = $this->getQueryInfo();
            $primaryProperty = $recordInfo[self::PRIMARY_PROPERTY];
            $getter = 'get' . ucfirst($primaryProperty);
            $setter = 'set' . ucfirst($primaryProperty);
            $primaryValue = call_user_func([$this, $getter]);
            $QueryExecutor = new QueryExecutor;
            if($primaryValue) {
                $queryData = call_user_func($queryInfo[self::UPDATE]);
                $QueryExecutor->query($queryData['query'], $queryData['param']);
            } else {
                $queryData = call_user_func($queryInfo[self::INSERT]);
                $QueryExecutor->query($queryData['query'], $queryData['param']);
                $primaryValue = $QueryExecutor->getLastId();
            }
            call_user_func([$this, $setter], $primaryValue);
            return $primaryValue;
        }
    }
    
    public function remove()
    {
        $queryInfo = $this->getQueryInfo();
        $queryData = call_user_func($queryInfo[self::DELETE]);
        $QueryExecutor = new QueryExecutor;
        $QueryExecutor->query($queryData['query'], $queryData['param']);
        $this->isValid = false;
    }

    public function toArray()
    {
        if($this->isValid) {
            $recordInfo = self::getRecordInfo();
            $propertyMap = $recordInfo[self::PROPERTY_MAP];
            $data = [];
            foreach($propertyMap as $property => $column) {
                $getter = 'get' . ucfirst($property);
                $data[$property] = call_user_func([$this, $getter]);
            }
            Return $data;
        }
    }

    public function isValid()
    {
        return $this->isValid;
    }

    
    protected function setAssiociate($assiociteInfo)
    {
        $recordInfo = self::getRecordInfo();
        $assiociateList = $recordInfo[Assiociate::ASSIOCIATE_LIST];
        foreach($assiociateList as $assiociateType => $assiociate) {
            foreach($assiociate as $target) {
                if($target[$assiociateType][Assiociate::TARGET_RECORD] === $assiociteInfo[Assiociate::ASSIOCIATE_RECORD_CLASS]) {
                    break 2;
                }
            }
        }
        $assiociateHelper = AssiociateHelper::class . '\\' . $assiociateType;
        if($assiociateHelper::setAssiociatedRecord($this, $recordInfo, $assiociteInfo, function($query, $param, $lazyFlag) use ($recordInfo) {
            if($lazyFlag) {
                self::$info[static::class][Assiociate::ASSIOCIATE_QUERY][Assiociate::LAZY] = [
                    'query' => $query,
                    'param' => $param,
                ];
                $columnMap = $recordInfo[self::COLUMN_MAP];
                foreach($columnMap as $property => $column) {
                    unset($this->{$property});
                }                
            } else {
                $raw = QueryExecutor::queryAndFetch($query, $param);
                $this->assign($raw);
            }
        })) {
            $assiociteRecord->setAssiociate([
                Assiociate::JOIN_COLUMN => $referencedJoinColumn,
                Assiociate::ASSIOCIATE_RECORD => $this
            ]);
        }
    }

    public function __get($property)
    {
        $recordInfo = self::getRecordInfo();
        $columnMap = $recordInfo[self::COLUMN_MAP];
        
        if(!isset($columnMap[$property])) {
            trigger_error('private');
        }
        extract($recordInfo[Assiociate::ASSIOCIATE_QUERY][Assiociate::LAZY]);
        foreach($param as $bindKey => $bindValue) {
            $param[$bindKey] = call_user_func($bindValue);
        }
        $raw = QueryExecutor::queryAndFetch($query, $param);
        $this->assign($raw);
        return $this->{$property};
    }

    private function fetchRaw($primaryValue) {
        $recordInfo = self::getRecordInfo();
        $queryInfo = $this->getQueryInfo();
        $primaryProperty = $recordInfo[self::PRIMARY_PROPERTY];
        $setter = [$this, 'set' . ucfirst($primaryProperty)];
        call_user_func($setter, $primaryValue);
        $queryData = call_user_func($queryInfo[self::SELECT]);
        $raw = QueryExecutor::queryAndFetch($queryData['query'], $queryData['param']);
        if($raw) {
            $this->assign($raw);
        } else {
            call_user_func($setter, null);
            $this->isValid = false;
        }
    }

    public function assign($raw)
    {
        $recordInfo = self::getRecordInfo();
        $propertyMap = $recordInfo[self::PROPERTY_MAP];
        foreach($propertyMap as $property => $column) {
            if(isset($raw[$column])) {
                $setter = 'set' . ucfirst($property);
                call_user_func([$this, $setter], $raw[$column]);
            }
        }
        if($this->isValid) {
            $this->makeAssiociate();
        }
    }

    static public function setSqlBuilder(SqlBuilderInterface $sqlbuilder)
    {
        self::$sqlbuilder = $sqlbuilder;
        $sqlbuilder->setRecordInfo(static::class, self::getRecordInfo());
    }
    
    static public function setModel(ModelInterface $Model)
    {
        self::$Model = $Model;
        $Model->setRecordInfo(self::getRecordInfo());
    }
    
    static public function getModel()
    {
        if(self::$Model === null) {
            $recordInfo = self::getRecordInfo();
            $modelClass = $recordInfo[self::ENTITY][self::MODEL_CLASS];
            self::$Model = $modelClass::getSingleton();
        }
        return self::$Model;
    }

    static public function getRecordInfo()
    {
        if(empty(self::$info[static::class])) {
            self::$info[static::class] = [
                self::TABLE => false,
                self::PRIMARY_KEY => false,
                self::ENTITY => [],
                self::PROPERTY => [],
                self::PROPERTY_MAP => null,
                Assiociate::ASSIOCIATE_LIST => null,
                Assiociate::ASSIOCIATE_QUERY => [],
                self::QUERY => [
                    self::SELECT => null,
                    self::INSERT => null,
                    self::UPDATE => null,
                    self::DELETE => null,
                ],
            ];
            $reflection = new ReflectionClass(static::class);
            self::makeEntityInfo($reflection->getDocComment());
            $propertyList = array_diff_key(
                get_class_vars(static::class),
                get_class_vars(__CLASS__)
            );
            foreach($propertyList as $property => $dummy) {
                $propertyDocComment = $reflection->getProperty($property)->getDocComment();
                self::makePropertyInfo($property, $propertyDocComment);
            }
            if(self::makeTableInfo()) {
                self::prepareQueryInfo();
            }
        }
        return self::$info[static::class];        
    }
    
    private function getQueryInfo()
    {
        if($this->queryInfo === null) {
            $this->makeQueryInfo();
        }
        return $this->queryInfo;
    }

    static private function makeEntityInfo($comment)
    {
        array_map(function($line) {
            if(strpos($line, '@ORM') == false) {
                return true;
            }
            $line = explode('@ORM\\', $line);
            $line = trim(str_replace(';', '', array_pop($line)));
            if(strpos($line, '(') && strpos($line, ')')) {
                list($name, $query) = explode('(', str_replace(')', '', $line));
                $query = str_replace([',', '\'', '"'], ['&', ''], $query);
                parse_str($query, $data);
            } else {
                $name = $line;
                $data = null;
            }
            
            if(isset(self::$info[static::class][$name])) {
                self::$info[static::class][$name] = $data;
            } else {
                self::$info[static::class][self::ENTITY][$name] = $data;
            }
        }, explode(PHP_EOL, $comment));
    }

    static private function makePropertyInfo($property, $comment)
    {
        array_map(function($line) use ($property) {
            if(strpos($line, '@ORM') == false) {
                return true;
            }
            $line = explode('@ORM\\', $line);
            $line = trim(str_replace(';', '', array_pop($line)));
            If($line === self::ID) {                
                self::$info[static::class][self::PRIMARY_PROPERTY] = $property;
                return true;
            }            
            if(strpos($line, '(') && strpos($line, ')')) {
                list($name, $query) = explode('(', str_replace(')', '', $line));
            }
            if(!isset(self::$info[static::class][self::PROPERTY][$property])) {
                self::$info[static::class][self::PROPERTY][$property] = [];
            }
            $query = str_replace([',', '\'', '"'], ['&', ''], $query);
            parse_str($query, $data);
            self::$info[static::class][self::PROPERTY][$property][$name] = $data;            
        }, explode(PHP_EOL, $comment));
    }
    
    static private function makeTableInfo()
    {
        $info = self::$info[static::class];
        if(!isset($info[self::TABLE])) {
            return false;
        }
        if(!isset($info[self::TABLE][self::NAME])) {
            return false;
        }        
        if(!$primaryProperty = $info[self::PRIMARY_PROPERTY]) {
            return false;
        }
        if(!isset($info[self::PROPERTY][$primaryProperty])) {
            return false;
        }
        if(!isset($info[self::PROPERTY][$primaryProperty][self::COLUMN])) {
            return false;
        }
        if(!isset($info[self::PROPERTY][$primaryProperty][self::COLUMN][self::NAME])) {
            return false;
        }
        self::$info[static::class][self::PRIMARY_KEY] = self::$info[static::class][self::PROPERTY][$primaryProperty][self::COLUMN][self::NAME];
        return true;
    }

    static private function prepareQueryInfo()
    {
        $info = self::$info[static::class];
        $propertyList = $info[self::PROPERTY];
        $propertyMap = [];
        $columnMap = [];
        $assiociteList = [
            Assiociate::ONE_TO_ONE => [],
            Assiociate::ONE_TO_MANY => [],
            Assiociate::MANY_TO_ONE => [],
        ];
        foreach($propertyList as $propertyName => $property) {
            if(isset($property[self::COLUMN]) && isset($property[self::COLUMN][self::NAME])) {
                $columnMap[$propertyName] = $propertyMap[$propertyName] = $property[self::COLUMN][self::NAME];                
            }
            if(isset($property[Assiociate::ONE_TO_ONE])) {
                $assiociteList[Assiociate::ONE_TO_ONE][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[Assiociate::ONE_TO_ONE][Assiociate::TARGET_RECORD] . '::'. $property[Assiociate::JOIN_COLUMN][Assiociate::REFERENCED_COLUMN_NAME];
            } else if (isset($property[Assiociate::ONE_TO_MANY])) {
                $assiociteList[Assiociate::ONE_TO_MANY][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[Assiociate::ONE_TO_MANY][Assiociate::TARGET_RECORD] . '::'. $property[Assiociate::JOIN_COLUMN][Assiociate::REFERENCED_COLUMN_NAME];
            } else if (isset($property[Assiociate::MANY_TO_ONE])) {
                $assiociteList[Assiociate::MANY_TO_ONE][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[Assiociate::MANY_TO_ONE][Assiociate::TARGET_RECORD] . '::'. $property[Assiociate::JOIN_COLUMN][Assiociate::REFERENCED_COLUMN_NAME];
            }
        }
        $info[self::COLUMN_MAP] = $columnMap;
        $info[self::PROPERTY_MAP] = $propertyMap;
        foreach($info[self::QUERY] as $queryType => $query) {
            $info[self::QUERY][$queryType] = self::prepareQuery($queryType, $columnMap, $info[self::TABLE][self::NAME], $info[self::PRIMARY_KEY]);
        }
        $info[Assiociate::ASSIOCIATE_LIST] = $assiociteList;
        self::$info[static::class] = $info;
    }

    static private function prepareQuery($queryType, $propertyMap, $table, $primaryKey)
    {
        $query = null;
        switch($queryType) {
        case self::SELECT:
            $query = SqlBuilder::makeSelectQuery($propertyMap, $table, $primaryKey);
            break;
        case self::INSERT:
            $query = SqlBuilder::makeInsertQuery($propertyMap, $table, $primaryKey);
            break;
        case self::UPDATE:
            $query = SqlBuilder::makeUpdateQuery($propertyMap, $table, $primaryKey);
            break;
        case self::DELETE:
            $query = SqlBuilder::makeDeleteQuery($propertyMap, $table, $primaryKey);
            break;
        }
        return $query;
    }

    private function makeQueryInfo()
    {
        $recordInfo = self::getRecordInfo();
        $this->queryInfo = [];
        foreach($recordInfo[self::QUERY] as $queryType => $query) {
            $this->queryInfo[$queryType] = $this->makeQuery($query, $queryType, $recordInfo[self::COLUMN_MAP], $recordInfo[self::PRIMARY_KEY]);
        }
    }

    public function makeQuery($query, $queryType, $columnMap, $primaryKey)
    {
        $recordInfo = self::getRecordInfo();
        $propertyMap = $recordInfo[self::PROPERTY_MAP];
        $executor = null;
        $option = ['queryType' => $queryType];
        switch($queryType) {
        case self::INSERT:
            $executor = $this->makeLazyQuery($query, $queryType, $columnMap, $option);
            break;
        case self::UPDATE:
            $executor = $this->makeLazyQuery($query, $queryType, $columnMap, $option);
            break;
        case self::SELECT:
            $primaryProperty = array_search($primaryKey, $propertyMap);
            $executor = $this->makeLazyQuery($query, $queryType, [$primaryProperty => $primaryKey], $option);
            break;
        case self::DELETE:
            $primaryProperty = array_search($primaryKey, $propertyMap);
            $executor = $this->makeLazyQuery($query, $queryType, [$primaryProperty => $primaryKey], $option);
            break;
        }
        return $executor;
    }

    private function makeAssiociate()
    {
        if($this->assiociateInitedFlag === false) {
            $assiociteList = self::$info[static::class][Assiociate::ASSIOCIATE_LIST];
            $propertyMap = self::$info[static::class][self::PROPERTY_MAP];
            foreach($assiociteList as $assiociateType => $assiocitePropertyList) {
                foreach($assiocitePropertyList as $propertyName => $property) {
                    $getter = 'get' . ucfirst($propertyName);
                    if($assiocites = call_user_func([$this, $getter])) {
                        continue;
                    }
                    $assiociateHelper = AssiociateHelper::class . '\\' . $assiociateType;
                    if($result = $assiociateHelper::makeAssiociateRecord($this, $propertyName, $property, self::$info[static::class][self::TABLE][self::NAME], $propertyMap)) {
                        list($RecordOrCollection, $param) = $result;
                        $RecordOrCollection->setAssiociate($param);
                    }
                }
            }
            $this->assiociateInitedFlag = true;
        }
    }

    private function makeLazyQuery($query, $queryType, $propertyMap, $option = null)
    {
        $paramGetter = [];
        return function () use ($query, $queryType, $propertyMap, $option) {
            foreach($propertyMap as $property => $column) {
                $getter = 'get' . ucfirst($property);
                $paramGetter[':' . $column] = [$this, $getter];
            }
            $this->queryInfo[$queryType] = function () use ($query, $paramGetter, $option) {
                foreach($paramGetter as $key => $getter) {
                    $paramGetter[$key] = call_user_func($getter);
                }
                return [
                    'query' => $query,
                    'param' => $paramGetter,
                    'option' => $option
                ];
            };
            return call_user_func($this->queryInfo[$queryType]);
        };
    }

    static public function setConfig ($config)
    {
        return self::$config = $config;
    }

    static public function getConfig ()
    {
        return self::$config;
    }   
}
