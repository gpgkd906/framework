<?php
namespace Framework\Module\Cngo\AdminTop;

use Framework\RouteModel\RouteModelInterface;
use Framework\ObjectManager\ObjectManager;
use Framework\Module\Cngo\AdminTop\Controller\IndexController;

ObjectManager::getSingleton()->get(RouteModelInterface::class)
    ->register([
        'admin/index' => IndexController::class
    ]);
