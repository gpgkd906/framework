<?php
declare(strict_types=1);

namespace Std\ModelManager;
use Iterator;
use Countable;

class ModelIterator implements Iterator, Countable
{
    private $position = 0;
    private $data = [];
    private $models = [];
    private $modelClass = null;

    public function __construct($modelClass, $data = null)
    {
        $this->position = 0;
        $this->modelClass = $modelClass;
        if ($data) {
            $this->data = $data;
        }
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        if (!isset($this->models[$this->position])) {
            if (!$this->valid()) {
                return null;
            }
            $this->models[$this->position] = ModelManager::getSingleton()->getModel($this->modelClass, $this->data[$this->position]);
        }
        return $this->models[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->data[$this->position]);
    }

    public function count()
    {
        return count($this->data);
    }
}
