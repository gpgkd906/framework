<?php

/**
 * ModelInterface
 *
 * [:package description]
 *
 * Copyright 2015 Chen Han
 *
 * Licensed under The MIT License
 *
 * @copyright Copyright 2015 Chen Han
 * @link
 * @since
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace FrameWork\Core\Interface;

/**
 * ModelInterface
 * [:class description]
 *
 * @author 2015 Chen Han
 * @package 
 * @link 
 */
class ModelInterface 
{
    static function getSingleton();
    
    public function find();

    public function order();

    public function group();

    public function join();

    public function select();

    public function update();

    public function delete();

    public function create();

    public function getRecord();

    public function getAllRecord();

    public function getArray();

    public function getAllArray();
    
    public function setFilters();
    
    public function getFilters();

    public function skipFilter();

    public function setRelations();

    public function getRelations();

    public function skipRelation();
}
