<?php
namespace Framework\Module\Cngo\Admin;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'admin/login' => Controller\LoginController::class,
        'admin/index' => Controller\DashboardController::class,
    ]);
