<?php

namespace Framework\Core\Interfaces;

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
