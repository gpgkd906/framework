<?php
namespace Framework\Application;


trait SingletonTrait {
    
    static private $instance = [];

    static public function getSingleton()
    {
        $className = static::class;
        if(!isset(self::$instance[$className])) {
            self::$instance[$className] = new $className();
        }
        return self::$instance[$className];        
    }
}
