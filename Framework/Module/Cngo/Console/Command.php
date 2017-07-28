<?php
namespace Framework\Module\Cngo\Console;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'cngo::module::crud::create' => Controller\Module\CrudCreateController::class,
        'cngo::module::controller::create' => Controller\Module\ControllerCreateController::class,
        'list' => Controller\ListController::class,
        'help' => Controller\HelpController::class,
        'doctrine' => Controller\DoctrineController::class,
    ]);
