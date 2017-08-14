<?php
declare(strict_types=1);
namespace Framework\Module\Cngo\Console;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'cngo::module::create' => Controller\Module\CreateController::class,
        'cngo::module::crud::create' => Controller\Module\CrudCreateController::class,
        'cngo::module::controller::create' => Controller\Module\ControllerCreateController::class,
        'cngo::module::entity::create' => Controller\Module\EntityCreateController::class,
        'list' => Controller\ListController::class,
        'help' => Controller\HelpController::class,
        'doctrine' => Controller\DoctrineController::class,
    ]);
