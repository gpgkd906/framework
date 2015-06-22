<?php
namespace Framework\Core;


trait SingletonTrait {
    
    static private $instance = [];

    static public function getSingleton()
    {
        $className = get_called_class();
        if(!isset(self::$instance[$className])) {
            self::$instance[$className] = new $className();
        }
        return self::$instance[$className];        
    }
}
