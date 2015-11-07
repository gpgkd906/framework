<?php

namespace Framework\Model\Model;

use Framework\Model\Model\QueryExecutor;

class SqlBuilder implements SqlBuilderInterface
{
    private $executor = null;
    private $parts = [
        'select' => null,
        'from' => null,
        'join' => null,
        'where' => null,
        'orderBy' => null,
        'groupBy' => null,
        'limit' => null,
        'having' => null,
    ];
    
    private function __construct()
    {
        $this->executor = new QueryExecutor;
    }
    
    public function createSqlBuilder()
    {
        return new self;
    }

    public function getOneResult()
    {
        $query = $this->getQuery();
        $data = $this->getParameters();
        $this->executor->query($query, $data);
        return $this->executor->fetch();
    }
    
    public function getResults()
    {
        $query = $this->getQuery();
        $data = $this->getParameters();
        $this->executor->query($query, $data);
        return $this->executor->fetchAll();
    }

    public function select($column)
    {

    }

    public function from()
    {

    }

    public function where()
    {

    }

    public function addWhere()
    {

    }

    public function orWhere()
    {

    }

    public function join()
    {

    }

    public function leftJoin()
    {

    }

    public function rightJoin()
    {

    }

    public function orderBy()
    {

    }

    public function groupBy()
    {
        
    }

    public function limit()
    {

    }

    public function having()
    {
        
    }
    
    public function getQuery()
    {
        
    }

    public function setParameters()
    {

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
            'SELECT * FROM `%s` JOIN `%s` ON `%s`.`%s` = `%s`.`%s` WHERE `%s`.`%s` = :%s',
            $table, $referencedTable, $table, $joinColumn, $referencedTable, $referencedJoinColumn, $table, $joinColumn, $joinColumn
        );
        return $query;
    }
}