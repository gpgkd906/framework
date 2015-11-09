<?php

namespace Framework\Model\Model;

use Framework\Model\Model\RecordInterface;

class Collection implements Countable, IteratorAggregate
{
    private $collection = [];

    public function add(RecordInterface $Record)
    {
        $this->collection[] = $Record;
    }
    
    public function getIterator()
    {
        return $this->collection;
    }

    public function count()
    {
        return count($this->collection);
    }

    
}
