<?php
namespace Framework\Module\Cngo\Front;

use Framework\Router\RouterManagerInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterManagerInterface::class)->get()
    ->register([
        'index' => Controller\IndexController::class,
    ]);
