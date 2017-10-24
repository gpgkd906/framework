<?php
namespace Project\Core\Front;

use Std\Router\RouterManagerInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterManagerInterface::class)->get()
    ->register([
        'index' => Controller\IndexController::class,
    ]);
