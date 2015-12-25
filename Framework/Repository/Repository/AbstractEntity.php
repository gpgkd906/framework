<?php

namespace Framework\Repository\Repository;

use Framework\Event\Event\EventTargetInterface;
use Framework\Repository\Repository\RepositoryInterface;
use Framework\Repository\Repository\SqlBuilderInterface;
use Framework\Repository\Repository\Collection;
use Framework\Repository\Repository\AssiociateHelper;
use Framework\Repository\Repository\AssiociateHelper\AssiociateHelperInterface as Assiociate;
use ReflectionClass;
use Closure;
use Exception;

abstract class AbstractEntity implements EntityTargetInterface, EventInterface
{
    use \Framework\Event\Event\EventTargetTrait;
    //
    const TRIGGER_PREINSERT  = 'preInsert';
    const TRIGGER_PREUPDATE  = 'preUpdate';
    const TRIGGER_PREDELETE  = 'preDelete';
    const TRIGGER_POSTINSERT = 'postInsert';
    const TRIGGER_POSTUPDATE = 'postUpdate';
    const TRIGGER_POSTDELETE = 'postDelete';
    
    static protected $info = [];
    static private $Repository = null;
    static private $config = null;
    static private $sqlbuilder = null;
    private $isValid = true;
    private $queryInfo = null;    
    private $assiociateInitedFlag = false;
    private $propertyAccessors = null;
    
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
            $recordInfo = static::getEntityInfo();
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
            $recordInfo = static::getEntityInfo();
            $columnMap = $recordInfo[self::COLUMN_MAP];
            $data = [];
            foreach($columnMap as $property => $column) {
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
        $recordInfo = static::getEntityInfo();
        $assiociateList = $recordInfo[Assiociate::ASSIOCIATE_LIST];
        foreach($assiociateList as $assiociateType => $assiociate) {
            foreach($assiociate as $target) {
                if($target[$assiociateType][Assiociate::TARGET_ENTITY] === $assiociteInfo[Assiociate::ASSIOCIATE_ENTITY_CLASS]) {
                    break 2;
                }
            }
        }
        $assiociateHelper = AssiociateHelper::class . '\\' . $assiociateType;
        if($assiociateHelper::setAssiociatedEntity($this, $recordInfo, $assiociteInfo, function($query, $param, $lazyFlag) use ($recordInfo) {
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
            $assiociteEntity->setAssiociate([
                Assiociate::JOIN_COLUMN => $referencedJoinColumn,
                Assiociate::ASSIOCIATE_ENTITY => $this
            ]);
        }
    }

    public function __get($property)
    {
        $recordInfo = static::getEntityInfo();
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
        $recordInfo = static::getEntityInfo();
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
        $recordInfo = static::getEntityInfo();
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

    public function propertyWalk(Closure $closure)
    {
        if($this->propertyAccessors === null) {
            $this->propertyAccessors = [];
            $recordInfo = static::getEntityInfo();
            $propertyMap = $recordInfo[self::PROPERTY_MAP];
            foreach($propertyMap as $property => $column) {
                $getter = 'get' . ucfirst($property);
                $setter = 'set' . ucfirst($property);
                $this->propertyAccessors[$property] = [
                    'getter' => [$this, $getter],
                    'setter' => [$this, $setter],
                ];
            }
        }
        foreach($this->propertyAccessors as $property => $accessor) {
            $propertyValue = call_user_func($accessor['getter']);
            $newValue = call_user_func($closure, $property, $propertyValue);
            if($newValue !== null && $newValue !== false) {
                call_user_func($accessor['setter'], $newValue);
            }
        }
    }    

    static public function setRepository(RepositoryInterface $Repository)
    {
        self::$Repository = $Repository;
        $Repository->setEntityInfo(static::getEntityInfo());
    }
    
    static public function getRepository()
    {
        if(self::$Repository === null) {
            $recordInfo = static::getEntityInfo();
            $modelClass = $recordInfo[self::ENTITY][self::MODEL_CLASS];
            self::$Repository = $modelClass::getSingleton();
        }
        return self::$Repository;
    }

    static public function getEntityInfo()
    {
        return MetaInfoManager::getEntityInfo(static::class);
    }
        
    private function getQueryInfo()
    {
        if($this->queryInfo === null) {
            $this->makeQueryInfo();
        }
        return $this->queryInfo;
    }

    private function makeQueryInfo()
    {
        $recordInfo = static::getEntityInfo();
        $this->queryInfo = [];
        foreach($recordInfo[self::QUERY] as $queryType => $query) {
            $this->queryInfo[$queryType] = $this->makeQuery($query, $queryType, $recordInfo[self::COLUMN_MAP], $recordInfo[self::PRIMARY_KEY]);
        }
    }

    public function makeQuery($query, $queryType, $columnMap, $primaryKey)
    {
        $recordInfo = static::getEntityInfo();
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
            $info = static::getEntityInfo();
            $assiociteList = $info[Assiociate::ASSIOCIATE_LIST];
            $propertyMap = $info[self::PROPERTY_MAP];
            foreach($assiociteList as $assiociateType => $assiocitePropertyList) {
                foreach($assiocitePropertyList as $propertyName => $property) {
                    $getter = 'get' . ucfirst($propertyName);
                    if($assiocites = call_user_func([$this, $getter])) {
                        continue;
                    }
                    $assiociateHelper = AssiociateHelper::class . '\\' . $assiociateType;
                    if($result = $assiociateHelper::makeAssiociateEntity($this, $propertyName, $property, $info[self::TABLE][self::NAME], $propertyMap)) {
                        list($EntityOrCollection, $param) = $result;
                        $EntityOrCollection->setAssiociate($param);
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

    static public function getReflection()
    {
        return new ReflectionClass(static::class);
    }
    
    static public function getBaseReflection()
    {
        return new ReflectionClass(__CLASS__);
    }
}
