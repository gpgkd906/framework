<?php
declare(strict_types=1);
namespace Framework\ObjectManager;

trait SingletonTrait
{

    private static $instance = [];

    public static function getSingleton()
    {
        $className = static::class;
        if (!isset(self::$instance[$className])) {
            self::$instance[$className] = new $className();
        }
        return self::$instance[$className];
    }
}
