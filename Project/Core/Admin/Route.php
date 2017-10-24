<?php
// @codingStandardsIgnoreFile
namespace Project\Core\Admin;

use Std\Router\RouterManagerInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterManagerInterface::class)->get()
    ->register([
        'admin' => Controller\DashboardController::class,
        'admin/index' => Controller\DashboardController::class,
    ]);
