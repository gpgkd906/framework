<?php
namespace Framework\Module\Cms\Admin;

use Framework\Router\RouterInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->get(RouterInterface::class)
    ->register([
        'admin/blog/list' => Controller\Blog\ListController::class,
        'admin/blog/register' => Controller\Blog\RegisterController::class,
        'admin/blog/edit' => Controller\Blog\EditController::class,
        'admin/blog/delete' => Controller\Blog\DeleteController::class,
    ]);
