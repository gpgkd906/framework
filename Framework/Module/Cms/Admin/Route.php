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
        'admin/section/list' => Controller\Section\ListController::class,
        'admin/section/register' => Controller\Section\RegisterController::class,
        'admin/section/edit' => Controller\Section\EditController::class,
        'admin/section/delete' => Controller\Section\DeleteController::class,
        'admin/page/list' => Controller\Page\ListController::class,
        'admin/page/register' => Controller\Page\RegisterController::class,
        'admin/page/edit' => Controller\Page\EditController::class,
        'admin/page/delete' => Controller\Page\DeleteController::class,
    ]);
