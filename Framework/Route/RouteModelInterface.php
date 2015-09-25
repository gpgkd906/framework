<?php

namespace Framework\Route;

interface RouteModelInterface 
{
    public static function getSingleton();

    public function dispatch();
    
    public function getController();

    public function getParam();

    public function redirect($controller, $action, $param = null);
    
    public function update();

    public function refresh();
}
