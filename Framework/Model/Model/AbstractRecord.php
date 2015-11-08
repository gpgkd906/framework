<?php

namespace Framework\Model\Model;

use Framework\Event\Event\EventInterface;
use Framework\Model\Model\ModelInterface;
use Framework\Model\Model\SqlBuilderInterface;
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
    const ASSIOCIATE    = 'Assiociate';
    const ONE_TO_ONE    = 'OneToOne';
    const ONE_TO_MANY   = 'OneToMany';
    const MANY_TO_ONE   = 'ManyToOne';
    const FETCH         = 'fetch';
    const LAZY          = 'LAZY';
    const JOIN_COLUMN   = 'JoinColumn';
    const TARGET_RECORD = 'targetRecord';
    const REFERENCED_COLUMN_NAME = 'referencedColumnName';
    const REFERENCED_COLUMN_VALUE = 'referencedColumnValue';
    const REFERENCED_TABLE = 'referencedTable';
    const ASSIOCIATE_LIST = 'assiociateList';
    const ASSIOCIATE_RECORD = 'assiociateRecord';
    const ASSIOCIATE_QUERY = 'assiociateQuery';
    //
    const TRIGGER_PREINSERT  = 'preInsert';
    const TRIGGER_PREUPDATE  = 'preUpdate';
    const TRIGGER_PREDELETE  = 'preDelete';
    const TRIGGER_POSTINSERT = 'postInsert';
    const TRIGGER_POSTUPDATE = 'postUpdate';
    const TRIGGER_POSTDELETE = 'postDelete';
    
    static protected $info = [];
    static private $Model = null;
    static private $sqlbuilder = null;
    private $isValid = true;
    private $queryInfo = null;
    
    public function __construct($primaryValue = null, $raw = null, $emptyRecord = false)
    {
        $this->getQueryInfo();
        if($emptyRecord) {
            return false;
        }
        if($raw !== null) {
            $this->assign($raw);
        } else {
            if($primaryValue !== null) {
                $this->fetchRaw($primaryValue);
            }
        }
        if($this->isValid) {
            $this->makeAssiocite();
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
            $Model = $this->getModel();
            if($primaryValue) {
                $queryData = call_user_func($queryInfo[self::UPDATE]);
                $Model->query($queryData['query'], $queryData['param']);
            } else {
                $queryData = call_user_func($queryInfo[self::INSERT]);
                $Model->query($queryData['query'], $queryData['param']);
                $primaryValue = $Model->getLastId();
            }
            call_user_func([$this, $setter], $primaryValue);
            return $primaryValue;
        }
    }
    
    public function remove()
    {
        $queryInfo = $this->getQueryInfo();
        $queryData = call_user_func($queryInfo[self::DELETE]);
        $Model = $this->getModel();
        $Model->query($queryData['query'], $queryData['param']);
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
            return $data;
        }
    }

    public function isValid()
    {
        return $this->isValid;
    }

    protected function setAssiociate($joinColumn, $assiociteInfo)
    {
        $recordInfo = self::getRecordInfo();
        $queryInfo = $this->getQueryInfo();
        $propertyMap = $recordInfo[self::PROPERTY_MAP];
        $assiociteRecord = $assiociteInfo[self::ASSIOCIATE_RECORD];
        $joinProperty = array_search($joinColumn, $propertyMap);
        $assiociteProperty = array_search(get_class($assiociteRecord) . '::' . $joinColumn, $propertyMap);
        $getter = [$this, 'get' . ucfirst($assiociteProperty)];
        if($assiociteRecord === call_user_func($getter)) {
            return false;
        }
        $referencedJoinColumn = $assiociteInfo[self::REFERENCED_COLUMN_NAME];
        $referencedTable = $assiociteInfo[self::REFERENCED_TABLE];
        $referencedJoinKey = $referencedTable . '_' . $referencedJoinColumn;
        $referencedQuery = SqlBuilder::makeAssiociteQuery($joinColumn, $recordInfo[self::TABLE][self::NAME], $referencedJoinColumn, $referencedTable, $propertyMap);
        $referencedJoinColumn = $assiociteInfo[self::REFERENCED_COLUMN_NAME];
        $referencedJoinProperty = array_search(get_class($assiociteRecord) . '::' . $referencedJoinColumn, $propertyMap);        
        //実際assiociateRecordにデータのマッピングはまだ実装してない、SqlBuilderの実装が必要ので、一旦stop
        if($assiociteInfo[self::FETCH] === self::LAZY) {
            self::$info[static::class][self::ASSIOCIATE_QUERY][self::LAZY] = [
                'query' => $referencedQuery,
                'param' => [':' . $referencedJoinColumn => $assiociteInfo[self::REFERENCED_COLUMN_VALUE]],
            ];
            $columnMap = $recordInfo[self::COLUMN_MAP];
            foreach($columnMap as $property => $column) {
                unset($this->{$property});
            }
        } else {
            //lazyでなければ、そのままassign
            $referencedColumnValue = call_user_func($assiociteInfo[self::REFERENCED_COLUMN_VALUE]);
            $raw = QueryExecutor::queryAndFetch($referencedQuery, [':' . $referencedJoinColumn => $referencedColumnValue]);
            $this->assign($raw);
        }
        if($referencedJoinProperty) {
            $setter = 'set' . ucfirst($referencedJoinProperty);
            call_user_func([$this, $setter], $assiociteRecord);
        }
        $assiociteRecord->setAssiociate($referencedJoinColumn, [
            self::ASSIOCIATE_RECORD => $this
        ]);
    }

    public function __get($property)
    {
        $recordInfo = self::getRecordInfo();
        $columnMap = $recordInfo[self::COLUMN_MAP];
        
        if(!isset($columnMap[$property])) {
            trigger_error('private');
        }
        extract($recordInfo[self::ASSIOCIATE_QUERY][self::LAZY]);
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

    private function assign($raw)
    {
        $recordInfo = self::getRecordInfo();
        $propertyMap = $recordInfo[self::PROPERTY_MAP];
        foreach($propertyMap as $property => $column) {
            if(isset($raw[$column])) {
                $setter = 'set' . ucfirst($property);
                call_user_func([$this, $setter], $raw[$column]);
            }
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
                self::ASSIOCIATE_LIST => null,
                self::ASSIOCIATE_QUERY => [],
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
            self::ONE_TO_ONE => [],
            self::ONE_TO_MANY => [],
            self::MANY_TO_ONE => [],
        ];
        foreach($propertyList as $propertyName => $property) {
            if(isset($property[self::COLUMN]) && isset($property[self::COLUMN][self::NAME])) {
                $columnMap[$propertyName] = $propertyMap[$propertyName] = $property[self::COLUMN][self::NAME];                
            }
            if(isset($property[self::ONE_TO_ONE])) {
                $assiociteList[self::ONE_TO_ONE][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[self::ONE_TO_ONE][self::TARGET_RECORD] . '::'. $property[self::JOIN_COLUMN][self::REFERENCED_COLUMN_NAME];
            } else if (isset($property[self::ONE_TO_MANY])) {
                $assiociteList[self::ONE_TO_MANY][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[self::ONE_TO_MANY][self::TARGET_RECORD] . '::'. $property[self::JOIN_COLUMN][self::REFERENCED_COLUMN_NAME];
            } else if (isset($property[self::MANY_TO_ONE])) {
                $assiociteList[self::MANY_TO_ONE][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[self::MANY_TO_ONE][self::TARGET_RECORD] . '::'. $property[self::JOIN_COLUMN][self::REFERENCED_COLUMN_NAME];
            }
        }
        $info[self::COLUMN_MAP] = $columnMap;
        $info[self::PROPERTY_MAP] = $propertyMap;
        foreach($info[self::QUERY] as $queryType => $query) {
            $info[self::QUERY][$queryType] = self::prepareQuery($queryType, $columnMap, $info[self::TABLE][self::NAME], $info[self::PRIMARY_KEY]);
        }
        $info[self::ASSIOCIATE_LIST] = $assiociteList;
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

    private function makeAssiocite()
    {
        $assiociteList = self::$info[static::class][self::ASSIOCIATE_LIST];
        $propertyMap = self::$info[static::class][self::PROPERTY_MAP];
        foreach($assiociteList as $assiociteType => $assiocitePropertyList) {
            foreach($assiocitePropertyList as $propertyName => $property) {
                $getter = 'get' . ucfirst($propertyName);
                if($assiociteRecord = call_user_func([$this, $getter])) {
                    continue;
                }
                if(isset($property[$assiociteType][self::FETCH]) && $property[$assiociteType][self::FETCH] === self::LAZY) {
                    $this->makeAssiociteRecord($assiociteType, $propertyName, $property, self::$info[static::class][self::TABLE][self::NAME], $propertyMap, self::LAZY);
                } else {
                    $this->makeAssiociteRecord($assiociteType, $propertyName, $property, self::$info[static::class][self::TABLE][self::NAME], $propertyMap);
                }
            }
        }
    }

    /**
     * 現在はOneToOneだけ対応、OneToMany, ManyToOneは追って追加
     * assiociteTypeを利用して対応
     */
    private function makeAssiociteRecord($assiociateType, $propertyName, $property, $table, $propertyMap, $fetch = null)
    {
        $setter = [$this, 'set' . ucfirst($propertyName)];
        $targetRecord = $property[self::ONE_TO_MANY][self::TARGET_RECORD];
        $joinColumn = $property[self::JOIN_COLUMN][self::NAME];
        $referencedJoinColumn = $property[self::JOIN_COLUMN][self::REFERENCED_COLUMN_NAME];
        $referencedJoinProperty = array_search($referencedJoinColumn, $propertyMap);        
        $assiociteRecord = new $targetRecord(null, null, true);
        call_user_func($setter, $assiociteRecord);
        $assiociteRecord->setAssiociate($joinColumn, [
            self::ASSIOCIATE => $assiociateType,
            self::ASSIOCIATE_RECORD => $this,
            self::REFERENCED_COLUMN_NAME => $referencedJoinColumn,
            self::REFERENCED_COLUMN_VALUE => [$this, 'get' . ucfirst($referencedJoinProperty)],
            self::REFERENCED_TABLE => $table,
            self::FETCH => $fetch,
        ]);
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
}
