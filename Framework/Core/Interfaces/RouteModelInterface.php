<?php

/**
 * RouteInterface
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
namespace FrameWork\Core\Interfaces;

/**
 * RouteInterface
 * [:class description]
 *
 * @author 2015 Chen Han
 * @package 
 * @link 
 */
interface RouteModelInterface 
{
    public static function getSingleton();

    public function dispatch();
    
    public function getMethod();

    public function getParam();

    public function redirect($controller, $action, $param = null);
    
    public function update();

    public function refresh();
}
