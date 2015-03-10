<?php

namespace Framework\Core\Interfaces\Model;

interface RecordInterface
{
    public function assign($data);
    
    public function save();

    public function delete();

    public function set($col, $value);

    public function get($col);
}
