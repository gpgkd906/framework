<?php

namespace Framework\Model\Model;

interface SchemaInterface
{
    public function getColumns();
    
    public function getIndexs();

    public function getPrimaryKey();

    public function getName();
}
