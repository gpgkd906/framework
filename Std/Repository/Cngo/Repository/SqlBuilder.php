<?php
declare(strict_types=1);

namespace Std\Repository\Repository;

use Std\Repository\Repository\QueryExecutor;
use Std\Repository\Repository\EntityInterface;

/**
 * sample:
 *      $sqlBuilder = SqlBuilder::createSqlBuilder();
 *      $sqlBuilder->select(Users\Entity::class)
 *                 ->from(Users\Entity::class, 'u')
 *                 ->join([Tickets\Entity::class, 't'], 'userId', 'WITH', 'userId')
 *                 ->where('u.userId = :userId')
 *                 ->setParameters([
 *                     ':userId' => 1
 *                 ]);
 *      var_dump($sqlBuilder->getOneResult());
 */
class SqlBuilder implements SqlBuilderInterface
{
    const SELECT = 'SELECT';
    const INSERT = 'INSERT';
    const UPDATE = 'UPDATE';
    const DELETE = 'DELETE';
    
    private $executor = null;
    private $head = null;
    private $set = [];
    private $selectEntity = null;
    private $from = null;
    private $table = null;
    private $recordClass = null;
    private $join = [];
    private $joinEntityClass = [];
    private $where = [];
    private $orderBy = '';
    private $groupBy = '';
    private $having = '';
    private $limit = '';
    private $parameters = [];
    static $recordInfo = [];
    private $alias = [];
    private $map = [];
    private $headType = 'SELECT';
    
    private function __construct()
    {
        $this->executor = new QueryExecutor;
    }
    
    static public function createSqlBuilder()
    {
        return new self;
    }

    public function getOneResult()
    {
        $query = $this->getQuery();
        $data = $this->getParameter();
        $this->executor->query($query, $data);
        $record = $this->executor->fetch();
        if ($this->selectEntity !== null) {
            $selectEntity = $this->selectEntity;
            $record = new $selectEntity($record);
        }
        return $record;
    }
    
    public function getResult()
    {
        $query = $this->getQuery();
        $data = $this->getParameter();
        $this->executor->query($query, $data);
        $result = $this->executor->fetchAll();
        if ($this->selectEntity !== null) {
            $selectEntity = $this->selectEntity;
            foreach ($result as $idx => $raw) {
                $result[$idx] = new $selectEntity($raw);
            }
        }
        return $result;
    }

    public function select($head)
    {
        $this->headType = self::SELECT;
        $this->head = $head;
        return $this;
    }
    
    public function update($table, $as = null)
    {
        if (!is_subclass_of($table, EntityInterface::class)) {
            throw new Exception('invalid Entity:' . $table);
        }
        $this->recordClass = $table;
        $table = $this->getEntityInfo($table)['Table']['name'];
        $this->table = $table;
        if ($as) {
            $this->alias[$table] = $as;            
            $table = '`' . $table . '` as ' . $as;
        }
        $this->headType = self::UPDATE;
        $this->head = $table;
        return $this;
    }

    public function delete($table, $as = null)
    {
        if (!is_subclass_of($table, EntityInterface::class)) {
            throw new Exception('invalid Entity:' . $table);
        }
        $this->recordClass = $table;
        $table = $this->getEntityInfo($table)['Table']['name'];
        $this->table = $table;
        if ($as) {
            $this->alias[$table] = $as;            
            $table = '`' . $table . '` as ' . $as;
        }
        $this->headType = self::DELETE;
        $this->head = $table;
        return $this;
    }

    public function set($set, $value)
    {
        $setNo = count($this->set);
        $setLabel = ':set' . $setNo;
        $this->set[] = $set . '=' . $setLabel;
        $this->setParameter($setLabel, $value);
    }
    
    public function from($table, $as = null)
    {
        if (!is_subclass_of($table, EntityInterface::class)) {
            throw new Exception('invalid Entity:' . $table);
        }
        $this->recordClass = $table;
        $table = $this->getEntityInfo($table)['Table']['name'];
        $this->table = $table;
        if ($as) {
            $this->alias[$table] = $as;            
            $table = '`' . $table . '` as ' . $as;
        }
        $this->from = $table;
        return $this;
    }

    public function where($where)
    {
        if (!empty($where)) {
            $this->where[] = $where;
        }
        return $this;
    }

    public function andWhere($where)
    {
        if (!empty($where)) {
            $this->where[] = 'AND (' . $where . ')';
        }
        return $this;
    }

    public function orWhere($where)
    {
        if (!empty($where)) {
            $this->where[] = 'OR (' . $where . ')';
        }
        return $this;
    }

    public function join($joinTable, $joinColumn, $withOrUse, $referencedJoinColumn)
    {
        return $this->makeJoin('INNER JOIN', $joinTable, $joinColumn, $withOrUse, $referencedJoinColumn);
    }

    public function leftJoin($joinTable, $joinColumn, $withOrUse, $referencedJoinColumn)
    {
        return $this->makeJoin('LEFT JOIN', $joinTable, $joinColumn, $withOrUse, $referencedJoinColumn);
    }

    public function rightJoin($joinTable, $joinColumn, $withOrUse, $referencedJoinColumn)
    {
        return $this->makeJoin('RIGHT JOIN', $joinTable, $joinColumn, $withOrUse, $referencedJoinColumn);
    }

    private function makeJoin($joinType, $joinTable, $joinProperty, $with, $referencedJoinProperty = null)
    {
        $as = null;
        $join = '';
        if (is_array($joinTable)) {
            list($joinTable, $as) = $joinTable;
        }
        if (!is_subclass_of($joinTable, EntityInterface::class)) {
            throw new Exception('invalid Entity:' . $joinTable);
        }
        $joinTableEntity = $joinTable;
        $joinEntityInfo = $this->getEntityInfo($joinTable);
        $joinTable = $joinEntityInfo['Table']['name'];
        $joinColumn = $joinEntityInfo['columnMap'][$joinProperty];
        $join = sprintf('%s `%s`', $joinType, $joinTable);
        if (isset($as)) {
            $this->alias[$joinTable] = $as;
            $join .= ' AS ' . $as;
        }
        if (strtoupper($with) === 'WITH') {
            $join .= sprintf(' ON %s', $referencedJoinProperty);
        } else {
            $join .= sprintf(' USING(%s)', $joinColumn);
        }
        $this->join[] = $join;
        $this->joinEntityClass[$joinTable] = $joinTableEntity;
        return $this;
    }

    public function orderBy($orderBy)
    {
        $set = [];
        foreach ($orderBy as $column => $order) {
            $set[] = $column . ' ' . $order;
        }
        $this->orderBy = 'ORDER BY ' . join(',', $set);
        return $this;
    }

    public function groupBy($groupBy)
    {
        $this->groupBy = 'GROUP By ' .$groupBy;
        return $this;
    }

    public function limit($offset, $limit = null)
    {
        if ($limit) {
            $this->limit = $offset . ', ' . $limit;
        } else {
            $this->limit = $offset;
        }
        return $this;
    }

    public function having($having)
    {
        if (!empty($this->groupBy)) {
            $this->having = ' HAVING(' . $having . ')';
        }
        return $this;
    }
    
    public function getQuery()
    {
        $this->makeMap();
        $query = trim(join(' ', [
            $this->getHead(),
            $this->getFrom(),
            $this->getJoin(),
            $this->getWhere(),
            $this->getGroupBy(),
            $this->getHaving(),
            $this->getOrderBy(),
            $this->limit,
        ]));
        return $query;
    }

    private function getHead()
    {
        switch($this->headType) {
        case self::SELECT: $head = $this->getSelect(); break;
            //case self::INSERT: $head = $this->getInsert(); break;
        case self::UPDATE: $head = $this->getUpdate(); break;
        case self::DELETE: $head = $this->getDelete(); break;
        }
        return $this->mapping($head);
    }

    private function getSelect()
    {
        $head = $this->head;
        if (in_array($head, $this->alias)) {
            $table = array_search($head, $this->alias);
            if ($table === $this->table) {
                $head = $this->recordClass;
            } elseif (isset($this->alias[$head])) {
                $head = $this->alias[$head];
            } else {
                throw new Exception('invalid schema: [%s] for select', $head);
            }
        }
        if (is_subclass_of($head, EntityInterface::class)) {
            $this->selectEntity = $head;
            $recordInfo = $this->getEntityInfo($head);
            $columnMap = $recordInfo['columnMap'];
            $table = '`' . $recordInfo['Table']['name'] . '`';
            $column = [];
            foreach ($columnMap as $property => $col) {
                $column[] = $table . '.`' . $property . '`'; 
            }
            $head = join(', ', $column);
        }
        $head = 'SELECT ' . $head;
        return $head;
    }

    private function getUpdate()
    {
        return 'UPDATE ' . $this->head;
    }

    private function getDelete()
    {
        return 'DELETE FROM ' . $this->head;        
    }

    private function getFrom()
    {
        if ($this->headType === self::SELECT) {
            return 'FROM ' . $this->from;
        }
        return '';
    }

    private function getJoin()
    {
        return $this->mapping(join(' ', $this->join));
    }
    
    private function getGroupBy()
    {
        return $this->mapping($this->groupBy);        
    }

    private function getHaving()
    {
        return $this->mapping($this->having);
    }

    private function getOrderBy()
    {
        return $this->mapping($this->orderBy);
    }

    private function getWhere()
    {
        return empty($this->where) ? '' : 'WHERE ' . $this->mapping(join(' AND ', $this->where));
    }

    private function makeMap()
    {
        $search = [];
        $replace = [];
        foreach ($this->alias as $table => $alias) {
            $search[] = '`' . $table . '`.';
            $search[] = $table . '.';
            $replace[] = $alias . '.';
            $replace[] = $alias . '.';
        }
        $temp = [];
        if (empty($this->joinEntityClass)) {
            $temp = $this->getEntityInfo($this->recordClass)['columnMap'];
        } else {
            $recordClass = $this->joinEntityClass;
            $recordClass[] = $this->recordClass;
            foreach ($recordClass as $record) {
                $recordInfo = $this->getEntityInfo($record);
                $table = $recordInfo['Table']['name'];
                if (isset($this->alias[$table])) {
                    $table = $this->alias[$table];
                }
                $_map = $recordInfo['columnMap'];
                foreach ($_map as $property => $column) {
                    $temp[$table . '.' . $property] = sprintf('`%s`.`%s`', $table, $column);
                }
            }
        }
        foreach ($temp as $key => $val) {
            $search[] = $key;
            $replace[] = $val;
        }
        $this->map = [
            'search' => $search,
            'replace' => $replace,
        ];
    }

    private function mapping($partSql)
    {
        if (!empty($partSql)) {
            return str_replace($this->map['search'], $this->map['replace'], str_replace('`', '', $partSql));
        }
        return $partSql;
    }

    public function setParameter($param, $value)
    {
        $this->parameters[$param] = $value;
    }

    public function setParameters($parameters)
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    public function getParameter()
    {
        return $this->parameters;
    }

    public function setEntityInfo($recordClass, $recordInfo)
    {
        self::$recordInfo[$recordClass] = $recordInfo;
    }

    public function getEntityInfo($recordClass)
    {
        if (!isset(self::$recordInfo[$recordClass])) {
            $recordClass::setSqlBuilder($this);
        }
        return self::$recordInfo[$recordClass];
    }

    static public function makeSelectQuery($propertyMap, $table, $selectKey)
    {
        $query = sprintf('SELECT %s FROM `%s` WHERE `%s` = :%s', '`' . join('`, `', $propertyMap) . '`', $table, $selectKey, $selectKey);
        return $query;
    }

    static public function makeInsertQuery($propertyMap, $table, $primaryKey)
    {
        $propertyMap = array_diff($propertyMap, [$primaryKey]);
        $query = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $table, '`' . join('`, `', $propertyMap) . '`', ':' . join(', :', $propertyMap));
        return $query;
    }

    static public function makeUpdateQuery($propertyMap, $table, $primaryKey)
    {
        $set = [];
        foreach ($propertyMap as $column) {
            if ($column === $primaryKey) continue;
            $set[] = sprintf('`%s` = :%s', $column, $column);
        }
        $query = sprintf('UPDATE `%s` SET %s WHERE `%s` = :%s', $table, join(', ', $set), $primaryKey, $primaryKey);
        return $query;
    }
    
    static public function makeDeleteQuery($propertyMap, $table, $deleteKey)
    {
        $query = sprintf('DELETE FROM `%s` WHERE `%s` = :%s', $table, $deleteKey, $deleteKey);
        return $query;
    }
    
    static public function makeAssiociateQuery($joinColumn, $table, $referencedJoinColumn, $referencedTable, $propertyMap)
    {
        $query = sprintf(
            'SELECT `%s`.* FROM `%s` JOIN `%s` ON `%s`.`%s` = `%s`.`%s` WHERE `%s`.`%s` = :%s',
            $table, $table, $referencedTable, $table, $joinColumn, $referencedTable, $referencedJoinColumn, $table, $joinColumn, $joinColumn
        );
        return $query;
    }
}