<?php
declare(strict_types=1);

namespace Std\Repository\Repository\AssiociateHelper;

use Std\Repository\Repository\AssiociateHelper\AssiociateHelperInterface;
use Std\Repository\Repository\AbstractEntity as Entity;
use Std\Repository\Repository\SqlBuilder;
use Exception;

class ManyToOne implements AssiociateHelperInterface
{
    const TYPE = 'ManyToOne';
    
    static public function makeAssiociateEntity($record, $propertyName, $property, $table, $propertyMap)
    {
        $fetch = null;
        if (isset($property[self::TYPE][self::FETCH]) && $property[self::TYPE][self::FETCH] === self::LAZY) {
            $fetch = self::LAZY;
        }
        $setter = [$record, 'set' . ucfirst($propertyName)];
        $targetEntity = $property[self::TYPE][self::TARGET_ENTITY];
        $joinColumn = $property[self::JOIN_COLUMN][Entity::NAME];
        $referencedJoinColumn = $property[self::JOIN_COLUMN][self::REFERENCED_COLUMN_NAME];
        $referencedJoinProperty = array_search($referencedJoinColumn, $propertyMap);        
        $assiociteEntity = new $targetEntity();
        call_user_func($setter, $assiociteEntity);
        $param = [
            self::JOIN_COLUMN => $joinColumn,
            self::ASSIOCIATE_ENTITY => $record,
            self::ASSIOCIATE_ENTITY_CLASS => get_class($record),
            self::REFERENCED_COLUMN_NAME => $referencedJoinColumn,
            self::REFERENCED_COLUMN_VALUE => [$record, 'get' . ucfirst($referencedJoinProperty)],
            self::REFERENCED_TABLE => $table,
            self::FETCH => $fetch,
        ];
        return [$assiociteEntity, $param];
    }
    
    static public function setAssiociatedEntity($Collection, $recordInfo, $assiociteInfo, $callback)
    {
        $joinColumn = $assiociteInfo[self::JOIN_COLUMN];
        $propertyMap = $recordInfo[Entity::PROPERTY_MAP];
        $assiociteEntity = $assiociteInfo[self::ASSIOCIATE_ENTITY];
        $joinProperty = array_search($joinColumn, $propertyMap);
        $assiociteProperty = array_search($assiociteInfo[self::ASSIOCIATE_ENTITY_CLASS] . '::' . $joinColumn, $propertyMap);
        $referencedJoinColumn = $assiociteInfo[self::REFERENCED_COLUMN_NAME];
        $referencedTable = $assiociteInfo[self::REFERENCED_TABLE];
        $referencedJoinKey = $referencedTable . '_' . $referencedJoinColumn;
        $referencedQuery = SqlBuilder::makeAssiociateQuery($joinColumn, $recordInfo[Entity::TABLE][Entity::NAME], $referencedJoinColumn, $referencedTable, $propertyMap);
        $referencedJoinColumn = $assiociteInfo[self::REFERENCED_COLUMN_NAME];
        $referencedJoinProperty = array_search(get_class($assiociteEntity) . '::' . $referencedJoinColumn, $propertyMap);        
        if ($assiociteInfo[self::FETCH] === self::LAZY) {
            $param = [':' . $referencedJoinColumn => $assiociteInfo[self::REFERENCED_COLUMN_VALUE]];
        } else {
            $param = [':' . $referencedJoinColumn => call_user_func($assiociteInfo[self::REFERENCED_COLUMN_VALUE])];
        }
        call_user_func($callback, $referencedQuery, $param, $assiociteInfo[self::FETCH] === self::LAZY);
        return true;
    }
}
