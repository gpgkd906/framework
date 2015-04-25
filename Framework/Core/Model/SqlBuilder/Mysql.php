<?php

namespace Framework\Core\Model\Sqlbuilder;

use Framework\Core\Interfaces\Model\SqlbuilderInterface;

class Mysql implements SqlbuilderInterface
{
    public function getQuery();
    
    public function setSubQuery($subQuery);
    
    public function getSubQuery();
    
    public function setOrder($order);

    public function setGroup($group);
    
    public function setLimit($limit);

    public function setCondition($condition);
    
    public function join($table, $alias);

    private function makeSelect();

    private function makeUpdate();

    private function makeDelete();

    private function makeHalfSql();

    private function makeWhere();
}
