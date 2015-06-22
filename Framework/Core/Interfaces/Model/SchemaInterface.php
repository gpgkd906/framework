<?php

namespace Framework\Core\Interfaces\Model;

interface SchemaInterface
{
    public function getColumns();
    
    public function getIndexs();

    public function getPrimaryKey();

    public function getName();
}
