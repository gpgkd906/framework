<?php

namespace Framework\Model\Model\AssiociateHelper;

use Framework\Model\Model\AssiociateHelper\AssiociateHelperInterface;
use Framework\Model\Model\AbstractRecord as Record;
use Framework\Model\Model\SqlBuilder;
use Exception;

class OneToMany implements AssiociateHelperInterface
{
    const TYPE = 'OneToMany';
    
    static public function makeAssiociateRecord($record, $propertyName, $property, $table, $propertyMap)
    {
        throw new Exception('not implements');
        $fetch = null;
        if(isset($property[self::TYPE][self::FETCH]) && $property[self::TYPE][self::FETCH] === self::LAZY) {
            $fetch = self::LAZY;
        }
        $setter = [$record, 'set' . ucfirst($propertyName)];
        $targetRecord = $property[self::TYPE][self::TARGET_RECORD];
        $joinColumn = $property[self::JOIN_COLUMN][Record::NAME];
        $referencedJoinColumn = $property[self::JOIN_COLUMN][self::REFERENCED_COLUMN_NAME];
        $referencedJoinProperty = array_search($referencedJoinColumn, $propertyMap);        
        $assiociteRecord = new $targetRecord();
        call_user_func($setter, $assiociteRecord);
        $param = [
            self::JOIN_COLUMN => $joinColumn,
            self::ASSIOCIATE_RECORD => $record,
            self::ASSIOCIATE_RECORD_CLASS => get_class($record),
            self::REFERENCED_COLUMN_NAME => $referencedJoinColumn,
            self::REFERENCED_COLUMN_VALUE => [$record, 'get' . ucfirst($referencedJoinProperty)],
            self::REFERENCED_TABLE => $table,
            self::FETCH => $fetch,
        ];
        return [$assiociteRecord, $param];
    }

    static public function setAssiociatedRecord($Collection, $recordInfo, $assiociteInfo, $callBack)
    {
        throw new Exception('not implements');
        $joinColumn = $assiociteInfo[self::JOIN_COLUMN];
        $propertyMap = $recordInfo[Record::PROPERTY_MAP];
        $assiociteRecord = $assiociteInfo[self::ASSIOCIATE_RECORD];
        $joinProperty = array_search($joinColumn, $propertyMap);
        $assiociteProperty = array_search($assiociteInfo[self::ASSIOCIATE_RECORD_CLASS] . '::' . $joinColumn, $propertyMap);
        $getter = [$record, 'get' . ucfirst($assiociteProperty)];
        if($assiociteRecord === call_user_func($getter)) {
            return false;
        }
        $referencedJoinColumn = $assiociteInfo[self::REFERENCED_COLUMN_NAME];
        $referencedTable = $assiociteInfo[self::REFERENCED_TABLE];
        $referencedJoinKey = $referencedTable . '_' . $referencedJoinColumn;
        $referencedQuery = SqlBuilder::makeAssiociateQuery($joinColumn, $recordInfo[Record::TABLE][Record::NAME], $referencedJoinColumn, $referencedTable, $propertyMap);
        $referencedJoinColumn = $assiociteInfo[self::REFERENCED_COLUMN_NAME];
        $referencedJoinProperty = array_search(get_class($assiociteRecord) . '::' . $referencedJoinColumn, $propertyMap);        
        if($assiociteInfo[self::FETCH] === self::LAZY) {
            $param = [':' . $referencedJoinColumn => $assiociteInfo[self::REFERENCED_COLUMN_VALUE]];
        } else {
            $param = [':' . $referencedJoinColumn => call_user_func($assiociteInfo[self::REFERENCED_COLUMN_VALUE])];
        }
        call_user_func($callBack, $referencedQuery, $param, $assiociteInfo[self::FETCH] === self::LAZY);
        if($referencedJoinProperty) {
            $setter = 'set' . ucfirst($referencedJoinProperty);
            call_user_func([$record, $setter], $assiociteRecord);
        }        
    }    
}
