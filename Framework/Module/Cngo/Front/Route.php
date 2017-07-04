<?php
namespace Framework\Module\Cngo\Front;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'index/index' => Controller\IndexController::class,
    ]);
