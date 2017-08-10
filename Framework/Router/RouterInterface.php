<?php
declare(strict_types=1);

namespace Framework\Router;

interface RouterInterface
{
    public static function getSingleton();

    public function dispatch();

    public function getRouterList();
}
