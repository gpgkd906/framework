<?php

namespace Framework\Model\Model;

use Framework\Model\Model\QueryExecutor;
use Framework\Model\Model\RecordInterface;

class SqlBuilder implements SqlBuilderInterface
{
    private $executor = null;
    private $head = null;
    private $from = null;
    private $join = [];
    private $where = [];
    private $orderBy = '';
    private $groupBy = '';
    private $having = '';
    private $limit = '';
    private $parameters = [];
    static $recordInfo = [];
        
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
        return $this->executor->fetch();
    }
    
    public function getResult()
    {
        $query = $this->getQuery();
        $data = $this->getParameter();
        $this->executor->query($query, $data);
        return $this->executor->fetchAll();
    }

    public function select($column)
    {
        if(is_subclass_of($column, RecordInterface::class)) {
            $column = $this->getRecordInfo($column)['columnMap'];
            $column = join(', ', $column);
        }
        $this->head = 'SELECT ' . $column;
        return $this;
    }

    public function from($table, $as = null)
    {
        if(is_subclass_of($table, RecordInterface::class)) {
            $table = $this->getRecordInfo($table)['Table']['name'];
        }
        if($as) {
            $table = $table . ' as ' . $as;
        }
        $this->table = $table;
        return $this;
    }

    public function where($where)
    {
        if(!empty($where)) {
            $this->where[] = $where;
        }
        return $this;
    }

    public function andWhere($where)
    {
        if(!empty($where)) {
            $this->where[] = 'AND (' . $where . ')';
        }
        return $this;
    }

    public function orWhere($where)
    {
        if(!empty($where)) {
            $this->where[] = 'OR (' . $where . ')';
        }
        return $this;
    }

    public function join($joinTable, $joinColumn, $withOrUse, $referencedJoinColumn)
    {
        return $this;
    }

    public function leftJoin($joinTable, $joinColumn, $withOrUse, $referencedJoinColumn)
    {
        return $this;
    }

    public function rightJoin($joinTable, $joinColumn, $withOrUse, $referencedJoinColumn)
    {
        return $this;
    }

    public function orderBy($orderBy)
    {
        $set = [];
        foreach($orderBy as $column => $order) {
            $set[] = $column . ' ' . $order;
        }
        $this->orderBy = 'ORDER BY ' . join(',', $set);
        return $this;
    }

    public function groupBy($groupby)
    {
        $this->groupBy = 'GROUP By ' .$groupBy;
        return $this;
    }

    public function limit($offset, $limit = null)
    {
        if($limit) {
            $this->limit = $offset . ', ' . $limit;
        } else {
            $this->limit = $offset;
        }
        return $this;
    }

    public function having($having)
    {
        if(!empty($this->groupBy)) {
            $this->having = ' HAVING(' . $having . ')';
        }
        return $this;
    }
    
    public function getQuery()
    {
        $query = trim(join(' ', [
            $this->head,
            'FROM',
            $this->table,
            join(' ', $this->join),
            empty($this->where) ? '' : 'WHERE ' . join(' ', $this->where),
            $this->groupBy,
            $this->having,
            $this->orderBy,
            $this->limit,            
        ]));
        return $query;
    }

    public function setParameter($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameter()
    {
        return $this->parameters;
    }

    public function setRecordInfo($recordClass, $recordInfo)
    {
        self::$recordInfo[$recordClass] = $recordInfo;
    }

    public function getRecordInfo($recordClass)
    {
        if(!isset(self::$recordInfo[$recordClass])) {
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
        foreach($propertyMap as $column) {
            if($column === $primaryKey) continue;
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
    
    static public function makeAssiociteQuery($joinColumn, $table, $referencedJoinColumn, $referencedTable, $propertyMap)
    {
        $query = sprintf(
            'SELECT `%s`.* FROM `%s` JOIN `%s` ON `%s`.`%s` = `%s`.`%s` WHERE `%s`.`%s` = :%s',
            $table, $table, $referencedTable, $table, $joinColumn, $referencedTable, $referencedJoinColumn, $table, $joinColumn, $joinColumn
        );
        return $query;
    }
}