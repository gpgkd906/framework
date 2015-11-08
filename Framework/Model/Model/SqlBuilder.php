<?php

namespace Framework\Model\Model;

use Framework\Model\Model\QueryExecutor;
use Framework\Model\Model\RecordInterface;

/**
 * sample:
 *      $sqlBuilder = SqlBuilder::createSqlBuilder();
 *      $sqlBuilder->select(Users\Record::class)
 *                 ->from(Users\Record::class, 'u')
 *                 ->join([Tickets\Record::class, 't'], 'userId', 'WITH', 'userId')
 *                 ->where('u.userId = :userId')
 *                 ->setParameter([
 *                     ':userId' => 1
 *                 ]);
 *      var_dump($sqlBuilder->getOneResult());
 */
class SqlBuilder implements SqlBuilderInterface
{
    private $executor = null;
    private $head = null;
    private $selectRecord = null;
    private $from = null;
    private $table = null;
    private $recordClass = null;
    private $join = [];
    private $joinRecordClass = [];
    private $where = [];
    private $orderBy = '';
    private $groupBy = '';
    private $having = '';
    private $limit = '';
    private $parameters = [];
    static $recordInfo = [];
    private $alias = [];
    
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
        if($this->selectRecord !== null) {
            $selectRecord = $this->selectRecord;
            $record = new $selectRecord(null, $record);
        }
        return $record;
    }
    
    public function getResult()
    {
        $query = $this->getQuery();
        $data = $this->getParameter();
        $this->executor->query($query, $data);
        $result = $this->executor->fetchAll();
        if($this->selectRecord !== null) {
            $selectRecord = $this->selectRecord;
            foreach($result as $idx => $raw) {
                $result[$idx] = new $selectRecord(null, $raw);
            }
        }
        return $result;
    }

    public function select($column)
    {
        if(is_subclass_of($column, RecordInterface::class)) {
            $this->selectRecord = $column;
            $recordInfo = $this->getRecordInfo($column);
            $columnMap = $recordInfo['columnMap'];
            $table = '`' . $recordInfo['Table']['name'] . '`';
            $column = [];
            foreach($columnMap as $property => $col) {
                $column[] = $table . '.`' . $col . '`'; 
            }
            $column = join(', ', $column);
        }
        $this->head = 'SELECT ' . $column;
        return $this;
    }

    public function from($table, $as = null)
    {
        if(!is_subclass_of($table, RecordInterface::class)) {
            throw new Exception('invalid Record');
        }
        $this->recordClass = $table;
        $table = $this->getRecordInfo($table)['Table']['name'];
        $this->table = $table;
        if($as) {
            $this->alias[$table] = $as;            
            $table = '`' . $table . '` as ' . $as;
        }
        $this->from = $table;
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

    private function makeJoin($joinType, $joinTable, $joinProperty, $with, $referencedJoinProperty = null)
    {
        $as = null;
        $join = '';
        if(is_array($joinTable)) {
            list($joinTable, $as) = $joinTable;
        }
        if(!is_subclass_of($joinTable, RecordInterface::class)) {
            throw new Exception('invalid Record');
        }
        $this->joinRecordClass[] = $joinTable;
        $joinRecordInfo = $this->getRecordInfo($joinTable);
        $joinTable = $joinRecordInfo['Table']['name'];
        $joinColumn = $joinRecordInfo['columnMap'][$joinProperty];
        $join = sprintf('%s `%s`', $joinType, $joinTable);
        if(isset($as)) {
            $this->alias[$joinTable] = $as;
            $join .= ' AS ' . $as;
        }
        if(strtoupper($with) === 'WITH') {
            $table = isset($this->alias[$this->table]) ? $this->alias[$this->table] : '`' . $this->table . '`';
            $joinTable = isset($this->alias[$joinTable]) ? $this->alias[$joinTable] : '`' . $joinTable . '`';
            $referencedJoinColumn = $this->getRecordInfo($this->recordClass)['columnMap'][$referencedJoinProperty];
            $join .= sprintf(' ON `%s`.`%s` = `%s`.`%s`', $joinTable, $joinColumn, $table, $referencedJoinColumn);
        } else {
            $join .= sprintf(' USING(%s)', $joinColumn);
        }
        $this->join[] = $join;
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
        $this->mapHeadWhere();
        $query = trim(join(' ', [
            $this->head,
            'FROM',
            $this->from,
            join(' ', $this->join),
            empty($this->where) ? '' : 'WHERE ' . $this->where,
            $this->groupBy,
            $this->having,
            $this->orderBy,
            $this->limit,            
        ]));
        return $query;
    }

    private function mapHeadWhere()
    {
        //1 pass: head;
        $search = [];
        $replace = [];
        foreach($this->alias as $table => $alias) {
            $search[] = '`' . $table . '`.';
            $search[] = $table . '.';
            $replace[] = '`' . $alias . '`.';
            $replace[] = $alias . '.';
        }
        $this->head = str_replace($search, $replace, $this->head);
        //カンマ整形
        while(strpos($this->head, ' ,')) {
            $this->head = str_replace(' ,', ',', $this->head);
        }
        while(strpos($this->head, ', ')) {
            $this->head = str_replace(', ', ',', $this->head);
        }
        $this->head = str_replace(',', ', ', $this->head);
        //2 pass: head, map, 
        $map = [];
        if(empty($this->joinRecordClass)) {
            $map = $this->getRecordInfo($this->recordClass)['columnMap'];
        } else {
            $recordClass = $this->joinRecordClass;
            $recordClass[] = $this->recordClass;
            foreach($recordClass as $record) {
                $recordInfo = $this->getRecordInfo($record);
                $table = $recordInfo['Table']['name'];
                if(isset($this->alias[$table])) {
                    $table = $this->alias[$table];
                }
                $_map = $recordInfo['columnMap'];
                foreach($_map as $property => $column) {
                    $map[$table . '.' . $property] = sprintf('`%s`.`%s`', $table, $column);
                    $map[$table . '.' . $property . ','] = sprintf('`%s`.`%s`,', $table, $column);
                }
            }            
        }
        $head = explode(' ', $this->head);
        foreach($head as $idx => $token) {
            $token = str_replace('`', '', $token);
            if(isset($map[$token])) {
                $head[$idx] = $map[$token];
            }            
        }
        $this->head = join(' ', $head);
        $where = explode(' ', join(' ', $this->where));
        foreach($where as $idx => $token) {
            $token = str_replace('`', '', $token);
            if(isset($map[$token])) {
                $where[$idx] = $map[$token];
            }
        }
        $this->where = join(' ', $where);
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