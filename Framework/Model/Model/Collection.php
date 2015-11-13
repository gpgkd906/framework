<?php

namespace Framework\Model\Model;

use Framework\Model\Model\RecordInterface;
use Framework\Model\Model\AssiociateHelper\AssiociateHelperInterface as Assiociate;
use Framework\Model\Model\AbstractRecord;
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
    
    public function add(RecordInterface $Record)
    {
        $this->collection[] = $Record;
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
            $targetRecordClass = $lazyQueryInfo['targetRecordClass'];
            if($lazyQueryInfo['param'] instanceof \Closure) {
                $param = call_user_func($lazyQueryInfo['param']);
            } else {
                $param = $lazyQueryInfo['param'];
            }
            $raws = QueryExecutor::queryAndFetchAll($lazyQueryInfo['query'], $param);
            $setter = $lazyQueryInfo['setter'];
            $assiociateRecord = $lazyQueryInfo['assiociateRecord'];
            foreach($raws as $raw) {
                $Record = new $targetRecordClass();
                call_user_func([$Record, $setter], $assiociateRecord);
                $Record->assign($raw);
                $this->add($Record);
            }
            $this->initFlag = true;
        }
        return $this->collection;
    }

    public function setAssiociate($assiociateInfo)
    {
        $targetRecordClass = $assiociateInfo[Assiociate::TARGET_RECORD_CLASS];

        $recordInfo = $targetRecordClass::getRecordInfo();
        $proprytyMap = $recordInfo[AbstractRecord::PROPERTY_MAP];
        $assiociateRecord = $assiociateInfo[Assiociate::ASSIOCIATE_RECORD];
        $assiociateRecordClass = $assiociateInfo[Assiociate::ASSIOCIATE_RECORD_CLASS];
        $joinProperty = array_search($assiociateRecordClass . '::' . $assiociateInfo[Assiociate::JOIN_COLUMN], $proprytyMap);
        $assiociateList = $recordInfo[Assiociate::ASSIOCIATE_LIST];
        foreach($assiociateList as $assiociateType => $assiociate) {
            foreach($assiociate as $target) {
                if($target[$assiociateType][Assiociate::TARGET_RECORD] === $assiociateInfo[Assiociate::ASSIOCIATE_RECORD_CLASS]) {
                    break 2;
                }
            }
        }
        $assiociateHelper = AssiociateHelper::class . '\\' . $assiociateType;
        $setter = 'set' . ucfirst($joinProperty);
        $assiociateHelper::setAssiociatedRecord($this, $recordInfo, $assiociateInfo, function($query, $param, $lazyFlag) use ($recordInfo, $targetRecordClass, $assiociateRecord, $setter) {
            if($lazyFlag) {
                $lazyParam = function() use ($param) {
                    return array_map(function ($item) {
                        return call_user_func($item);
                    }, $param);                    
                };
                $this->lazyQueryInfo = [
                    'setter' => $setter,
                    'assiociateRecord' => $assiociateRecord,
                    'targetRecordClass' => $targetRecordClass,
                    'query' => $query,
                    'param' => $lazyParam
                ];
            } else {
                if($raws = QueryExecutor::queryAndFetchAll($query, $param)) {
                    foreach($raws as $raw) {
                        $Record = new $targetRecordClass();
                        call_user_func([$Record, $setter], $assiociateRecord);
                        $Record->assign($raw);
                        $this->add($Record);
                    }
                }                
            }
        });
    }    
}
