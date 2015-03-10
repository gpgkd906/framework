<?php

namespace Framework\Core\Interfaces\Model;

interface SchemaInterface
{
    public function getSingleton();

    public function getColumns($key);
    
    public function getIndexs($key);

    public function getForeignKey($key);
    
    public function getPrimaryKey();

    public function getName();
}
