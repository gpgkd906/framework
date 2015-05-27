<?php

namespace Framework\Core\Interfaces\Model;

interface SqlBuilderInterface 
{
    public function getQuery();

    public function getParameters();
    
    public function setSubQuery($subQuery);
    
    public function getSubQuery();
    
    public function addOrder($column, $order);
    
    public function setOrder($orderQuery);

    public function getOrder();

    public function addGroup($column);

    public function setGroup($groupQuery);

    public function getGroup();

    public function having($subQuery);
    
    public function limit($limit);

    public function find($where, $bind, $opera);
    
    public function join($joinModel, $leftCol, $rightCol);
}