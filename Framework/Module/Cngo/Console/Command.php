<?php
namespace Framework\Module\Cngo\Console;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'cngo::module::create' => Controller\Module\CreateController::class,
    ]);
