<?php

namespace Framework\Model\Model;

interface RecordInterface
{
    public function assign($data);
    
    public function save();

    public function delete();

    public function set($col, $value);

    public function get($col);
}
