<?php
namespace Framework\Module\Cngo\Admin;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;
use Framework\Module\Cngo\Admin\Controller;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'admin/login' => Controller\LoginController::class,
        'admin/index' => Controller\DashboardController::class,
    ]);
