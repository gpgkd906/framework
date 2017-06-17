<?php
namespace Framework\Module\Cngo\Admin;

use Framework\RouteModel\RouteModelInterface;
use Framework\ObjectManager\ObjectManager;
use Framework\Module\Cngo\Admin\Controller;

ObjectManager::getSingleton()->get(RouteModelInterface::class)
    ->register([
        'admin/login' => Controller\LoginController::class,
        'admin/index' => Controller\DashboardController::class,
    ]);
