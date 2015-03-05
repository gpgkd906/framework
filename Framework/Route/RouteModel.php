<?php

namespace Framework\Route;

use Framework\Core\Interfaces\RouteModelInterface;

class RouteModel implements RouteModelInterface
{
    static private $instance = null;

    private function __construct() {}

    public static function getSingleton()
    {
        if(self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;        
    }
    
    public function dispatch()
    {
        
    }
    
    public function redirect($controller, $action, $param = null)
    {
        
    }
    
    public function update()
    {
        
    }

    public function refresh()
    {
        
    }
}
