<?php
namespace Framework\Module\Cngo\AdminUser;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'admin/login' => Controller\LoginController::class,
        'admin/logout' => Controller\LogoutController::class,
        'admin/users/list' => Controller\Users\ListController::class,
        'admin/users/register' => Controller\Users\RegisterController::class,
        'admin/users/editor' => Controller\Users\EditController::class,
        'admin/users/delete' => Controller\Users\DeleteController::class,
    ]);
