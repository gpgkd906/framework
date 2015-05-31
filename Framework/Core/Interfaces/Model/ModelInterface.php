<?php

namespace Framework\Core\Interfaces\Model;

interface ModelInterface 
{
    static function getSingleton();
    
    public function find($column, $bind, $opera);

    public function order($column, $order);

    public function group($column);

    public function join();

    public function select($select);

    public function update();

    public function delete();

    public function create($data);

    public function get();

    public function getAll();

    public function getArray();

    public function getAllArray();    
}
