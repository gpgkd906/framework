<?php
declare(strict_types=1);

namespace Std\Repository\Repository;

use Framework\ObjectManager\SingletonInterface;
use Std\Repository\Repository\SqlBuilder;
use Std\Repository\Repository\AbstractEntity;
use Exception;

abstract class AbstractRepository implements RepositoryInterface, SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    /**
     *
     * @api
     * @var mixed $recordInfo 
     * @access private
     * @link
     */
    private $recordInfo = null;

    /**
     * 
     * @api
     * @param mixed $recordInfo
     * @return mixed $recordInfo
     * @link
     */
    public function setEntityInfo ($recordInfo)
    {
        return $this->recordInfo = $recordInfo;
    }

    /**
     * 
     * @api
     * @return mixed $recordInfo
     * @link
     */
    public function getEntityInfo ()
    {
        return $this->recordInfo;
    }

    
    public function __construct()
    {
        $record = static::ENTITY;
        $record::setRepository($this);
    }
    
    public function findBy($condition)
    {
        $sqlBuilder = SqlBuilder::createSqlBuilder();
        $sqlBuilder->select(static::ENTITY)
                   ->from(static::ENTITY);
        $recordInfo = $this->getEntityInfo();
        $propertyMap = $recordInfo[AbstractEntity::PROPERTY_MAP];
        $where = [];
        $param = [];
        foreach ($condition as $property => $value) {
            if (isset($propertyMap[$property])) {
                $column = $propertyMap[$property];
                $bindKey = ':' . $column;
                if (is_array($value)) {
                    $sub = [];
                    foreach ($value as $idx => $val) {
                        $param[$bindKey . $idx] = $val;
                        $sub[] = $bindKey . $idx;
                    }
                    $where[] = $propertyMap[$property] . ' in (' . join(', ', $sub) . ')';
                } else {
                    $param[$bindKey] = $value;
                    $where[] = $propertyMap[$property] . ' = ' . $bindKey;
                }
            }
        }
        $where = join(' AND ', $where);
        $sqlBuilder->where($where)
                   ->setParameter($param);        
        return $sqlBuilder->getResult();
    }

    public function find($id)
    {
        $result = false;
        if (is_numeric($id)) {
            $record = static::ENTITY;
            $record = new $record($id);
            if ($record->isValid()) {
                $result = $record;
            }
        }
        return $result;
    }
}
