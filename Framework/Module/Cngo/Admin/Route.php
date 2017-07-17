<?php
namespace Framework\Module\Cngo\Admin;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'admin/login' => Controller\LoginController::class,
        'admin/logout' => Controller\LogoutController::class,
        'admin' => Controller\DashboardController::class,
        'admin/index' => Controller\DashboardController::class,
    ]);
