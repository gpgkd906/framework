<?php

namespace Framework\Core\Interfaces\Model;

interface SqlBuilderInterface 
{
    public function getQuery();

    public function setSubQuery($subQuery);
    
    public function getSubQuery();
    
    public function setOrder($order);

    public function setGroup($group);
    
    public function setLimit($limit);

    public function setCondition($condition);
    
    public function join($table, $alias);
}