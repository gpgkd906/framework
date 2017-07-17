<?php

namespace Framework\Router;

trait RouterAwareTrait
{
    private static $Router;

    public function setRouter(RouterInterface $Router)
    {
        self::$Router = $Router;
    }

    public function getRouter()
    {
        return self::$Router;
    }
}
