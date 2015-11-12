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
        if($this->collection->count() === 0) {
            $lazyQueryInfo = $this->lazyQueryInfo;
            $targetRecordClass = $lazyQueryInfo['targetRecordClass'];
            $raws = QueryExecutor::queryAndFetchAll($lazyQueryInfo['query'], $lazyQueryInfo['param']);
            $setter = $lazyQueryInfo['setter'];
            $assiociateRecord = $lazyQueryInfo['assiociateRecord'];
            foreach($raws as $raw) {
                $Record = new $targetRecordClass();
                call_user_func([$Record, $setter], $assiociateRecord);
                $Record->assign($raw);
                $this->add($Record);
            }            
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
                $this->lazyQueryInfo = [
                    'setter' => $setter,
                    'assiociateRecord' => $assiociateRecord,
                    'targetRecordClass' => $targetRecordClass,
                    'query' => $query,
                    'param' => $param,
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
