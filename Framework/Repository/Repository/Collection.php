<?php

namespace Framework\Repository\Repository;

use Framework\Repository\Repository\EntityInterface;
use Framework\Repository\Repository\AssiociateHelper\AssiociateHelperInterface as Assiociate;
use Framework\Repository\Repository\AbstractEntity;
use Countable;
use IteratorAggregate;
use ArrayIterator;

class Collection implements Countable, IteratorAggregate
{
    private $lazyQueryInfo = [];
    
    private $collection = null;

    private $initFlag = false;

    public function __construct()
    {
        $this->collection = new ArrayIterator();
    }
    
    public function add(EntityInterface $Entity)
    {
        $this->collection[] = $Entity;
    }
    
    public function getIterator()
    {
        $collection = $this->getCollection();
        return $collection;
    }

    public function count()
    {
        return count($this->getCollection());
    }

    public function getCollection()
    {
        if($this->initFlag === false) {
            $lazyQueryInfo = $this->lazyQueryInfo;
            $targetEntityClass = $lazyQueryInfo['targetEntityClass'];
            if($lazyQueryInfo['param'] instanceof \Closure) {
                $param = call_user_func($lazyQueryInfo['param']);
            } else {
                $param = $lazyQueryInfo['param'];
            }
            $raws = QueryExecutor::queryAndFetchAll($lazyQueryInfo['query'], $param);
            $setter = $lazyQueryInfo['setter'];
            $assiociateEntity = $lazyQueryInfo['assiociateEntity'];
            foreach($raws as $raw) {
                $Entity = new $targetEntityClass();
                call_user_func([$Entity, $setter], $assiociateEntity);
                $Entity->assign($raw);
                $this->add($Entity);
            }
            $this->initFlag = true;
        }
        return $this->collection;
    }

    public function setAssiociate($assiociateInfo)
    {
        $targetEntityClass = $assiociateInfo[Assiociate::TARGET_ENTITY_CLASS];

        $recordInfo = $targetEntityClass::getEntityInfo();
        $proprytyMap = $recordInfo[AbstractEntity::PROPERTY_MAP];
        $assiociateEntity = $assiociateInfo[Assiociate::ASSIOCIATE_ENTITY];
        $assiociateEntityClass = $assiociateInfo[Assiociate::ASSIOCIATE_ENTITY_CLASS];
        $joinProperty = array_search($assiociateEntityClass . '::' . $assiociateInfo[Assiociate::JOIN_COLUMN], $proprytyMap);
        $assiociateList = $recordInfo[Assiociate::ASSIOCIATE_LIST];
        foreach($assiociateList as $assiociateType => $assiociate) {
            foreach($assiociate as $target) {
                if($target[$assiociateType][Assiociate::TARGET_ENTITY] === $assiociateInfo[Assiociate::ASSIOCIATE_ENTITY_CLASS]) {
                    break 2;
                }
            }
        }
        $assiociateHelper = AssiociateHelper::class . '\\' . $assiociateType;
        $setter = 'set' . ucfirst($joinProperty);
        $assiociateHelper::setAssiociatedEntity($this, $recordInfo, $assiociateInfo, function($query, $param, $lazyFlag) use ($recordInfo, $targetEntityClass, $assiociateEntity, $setter) {
            if($lazyFlag) {
                $lazyParam = function() use ($param) {
                    return array_map(function ($item) {
                        return call_user_func($item);
                    }, $param);                    
                };
                $this->lazyQueryInfo = [
                    'setter' => $setter,
                    'assiociateEntity' => $assiociateEntity,
                    'targetEntityClass' => $targetEntityClass,
                    'query' => $query,
                    'param' => $lazyParam
                ];
            } else {
                if($raws = QueryExecutor::queryAndFetchAll($query, $param)) {
                    foreach($raws as $raw) {
                        $Entity = new $targetEntityClass();
                        call_user_func([$Entity, $setter], $assiociateEntity);
                        $Entity->assign($raw);
                        $this->add($Entity);
                    }
                }                
            }
        });
    }    
}
