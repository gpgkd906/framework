<?php
declare(strict_types=1);

namespace Framework\Repository\Repository;

use Framework\Service\SessionService\SessionService;
use Framework\Repository\Repository\SqlBuilderInterface;
use Framework\Repository\Repository\AssiociateHelper\AssiociateHelperInterface as Assiociate;
use ReflectionClass;
use Exception;

class MetaInfoManager
{
    static protected $info = [];

    static private $baseProperties = null;

    static public function getEntityInfo($Entity)
    {
        if (empty(self::$info[$Entity])) {
            $SessionService = SessionService::getSingleton();
            $SessionKey = __CLASS__ . '::' . $Entity;
            if ($EntityInfo = $SessionService->getSection($SessionKey)) {
                self::$info[$Entity] = $EntityInfo;
            }
            if (empty(self::$info[$Entity])) {
                self::$info[$Entity] = [
                    EntityInterface::TABLE => false,
                    EntityInterface::PRIMARY_KEY => false,
                    EntityInterface::ENTITY => [],
                    EntityInterface::PROPERTY => [],
                    EntityInterface::PROPERTY_MAP => null,
                    Assiociate::ASSIOCIATE_LIST => null,
                    Assiociate::ASSIOCIATE_QUERY => [],
                    EntityInterface::QUERY => [
                        EntityInterface::SELECT => null,
                        EntityInterface::INSERT => null,
                        EntityInterface::UPDATE => null,
                        EntityInterface::DELETE => null,
                    ],
                ];
                $reflection = $Entity::getReflection();
                self::makeEntityInfo($Entity, $reflection->getDocComment());
                $propertyList = array_diff(
                    self::getProperties($reflection),
                    self::getProperties($Entity::getBaseReflection())
                );
                foreach ($propertyList as $property) {
                    $propertyDocComment = $reflection->getProperty($property)->getDocComment();
                    self::makePropertyInfo($Entity, $property, $propertyDocComment);
                }
                $SessionService->setSection($SessionKey, self::$info[$Entity]);
            }
        }
        if (empty(self::$info[$Entity][EntityInterface::PRIMARY_KEY])) {
            if (self::makeTableInfo($Entity)) {
                self::prepareQueryInfo($Entity);
            }
        }
        return self::$info[$Entity];        
    }

    static private function getProperties($reflection)
    {
        $propertyList = [];
        foreach ($reflection->getProperties() as $property) {
            $propertyList[] = $property->getName();
        }
        return $propertyList;
    }
        
    static private function makeEntityInfo($Entity, $comment)
    {
        array_map(function($line) use ($Entity) {
            if (strpos($line, '@ORM') == false) {
                return true;
            }
            $line = explode('@ORM\\', $line);
            $line = trim(str_replace(';', '', array_pop($line)));
            if (strpos($line, '(') && strpos($line, ')')) {
                list($name, $query) = explode('(', str_replace(')', '', $line));
                $query = str_replace([',', '\'', '"'], ['&', ''], $query);
                parse_str($query, $data);
            } else {
                $name = $line;
                $data = null;
            }
            
            if (isset(self::$info[$Entity][$name])) {
                self::$info[$Entity][$name] = $data;
            } else {
                self::$info[$Entity][EntityInterface::ENTITY][$name] = $data;
            }
        }, explode(PHP_EOL, $comment));
    }

    static private function makePropertyInfo($Entity, $property, $comment)
    {
        array_map(function($line) use ($Entity, $property) {
            if (strpos($line, '@ORM') == false) {
                return true;
            }
            $line = explode('@ORM\\', $line);
            $line = trim(str_replace(';', '', array_pop($line)));
            If($line === EntityInterface::ID) {                
                self::$info[$Entity][EntityInterface::PRIMARY_PROPERTY] = $property;
                return true;
            }            
            if (strpos($line, '(') && strpos($line, ')')) {
                list($name, $query) = explode('(', str_replace(')', '', $line));
            }
            if (!isset(self::$info[$Entity][EntityInterface::PROPERTY][$property])) {
                self::$info[$Entity][EntityInterface::PROPERTY][$property] = [];
            }
            $query = str_replace([',', '\'', '"'], ['&', ''], $query);
            parse_str($query, $data);
            self::$info[$Entity][EntityInterface::PROPERTY][$property][$name] = $data;            
        }, explode(PHP_EOL, $comment));
    }
    
    static private function makeTableInfo($Entity)
    {
        $info = self::$info[$Entity];
        if (!isset($info[EntityInterface::TABLE])) {
            return false;
        }
        if (!isset($info[EntityInterface::TABLE][EntityInterface::NAME])) {
            return false;
        }        
        if (!$primaryProperty = $info[EntityInterface::PRIMARY_PROPERTY]) {
            return false;
        }
        if (!isset($info[EntityInterface::PROPERTY][$primaryProperty])) {
            return false;
        }
        if (!isset($info[EntityInterface::PROPERTY][$primaryProperty][EntityInterface::COLUMN])) {
            return false;
        }
        if (!isset($info[EntityInterface::PROPERTY][$primaryProperty][EntityInterface::COLUMN][EntityInterface::NAME])) {
            return false;
        }
        self::$info[$Entity][EntityInterface::PRIMARY_KEY] = self::$info[$Entity][EntityInterface::PROPERTY][$primaryProperty][EntityInterface::COLUMN][EntityInterface::NAME];
        return true;
    }

    static private function prepareQueryInfo($Entity)
    {
        $info = self::$info[$Entity];
        $propertyList = $info[EntityInterface::PROPERTY];
        $propertyMap = [];
        $columnMap = [];
        $assiociteList = [
            Assiociate::ONE_TO_ONE => [],
            Assiociate::ONE_TO_MANY => [],
            Assiociate::MANY_TO_ONE => [],
        ];
        foreach ($propertyList as $propertyName => $property) {
            if (isset($property[EntityInterface::COLUMN]) && isset($property[EntityInterface::COLUMN][EntityInterface::NAME])) {
                $columnMap[$propertyName] = $propertyMap[$propertyName] = $property[EntityInterface::COLUMN][EntityInterface::NAME];                
            }
            if (isset($property[Assiociate::ONE_TO_ONE])) {
                $assiociteList[Assiociate::ONE_TO_ONE][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[Assiociate::ONE_TO_ONE][Assiociate::TARGET_ENTITY] . '::'. $property[Assiociate::JOIN_COLUMN][Assiociate::REFERENCED_COLUMN_NAME];
            } else if (isset($property[Assiociate::ONE_TO_MANY])) {
                $assiociteList[Assiociate::ONE_TO_MANY][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[Assiociate::ONE_TO_MANY][Assiociate::TARGET_ENTITY] . '::'. $property[Assiociate::JOIN_COLUMN][Assiociate::REFERENCED_COLUMN_NAME];
            } else if (isset($property[Assiociate::MANY_TO_ONE])) {
                $assiociteList[Assiociate::MANY_TO_ONE][$propertyName] = $property;
                $propertyMap[$propertyName] = $property[Assiociate::MANY_TO_ONE][Assiociate::TARGET_ENTITY] . '::'. $property[Assiociate::JOIN_COLUMN][Assiociate::REFERENCED_COLUMN_NAME];
            }
        }
        $info[EntityInterface::COLUMN_MAP] = $columnMap;
        $info[EntityInterface::PROPERTY_MAP] = $propertyMap;
        foreach ($info[EntityInterface::QUERY] as $queryType => $query) {
            $info[EntityInterface::QUERY][$queryType] = self::prepareQuery($queryType, $columnMap, $info[EntityInterface::TABLE][EntityInterface::NAME], $info[EntityInterface::PRIMARY_KEY]);
        }
        $info[Assiociate::ASSIOCIATE_LIST] = $assiociteList;
        self::$info[$Entity] = $info;
    }

    static private function prepareQuery($queryType, $propertyMap, $table, $primaryKey)
    {
        $query = null;
        switch($queryType) {
        case EntityInterface::SELECT:
            $query = SqlBuilder::makeSelectQuery($propertyMap, $table, $primaryKey);
            break;
        case EntityInterface::INSERT:
            $query = SqlBuilder::makeInsertQuery($propertyMap, $table, $primaryKey);
            break;
        case EntityInterface::UPDATE:
            $query = SqlBuilder::makeUpdateQuery($propertyMap, $table, $primaryKey);
            break;
        case EntityInterface::DELETE:
            $query = SqlBuilder::makeDeleteQuery($propertyMap, $table, $primaryKey);
            break;
        }
        return $query;
    }    
}