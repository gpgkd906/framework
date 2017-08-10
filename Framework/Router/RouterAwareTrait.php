<?php
declare(strict_types=1);

namespace Framework\Router;

use Framework\ObjectManager\ObjectManager;

trait RouterAwareTrait
{
    private static $Router;

    public function setRouter(RouterInterface $Router)
    {
        self::$Router = $Router;
    }

    public function getRouter()
    {
        if (self::$Router === null) {
            self::$Router = ObjectManager::getSingleton()->get(RouterInterface::class);
        }
        return self::$Router;
    }
}
