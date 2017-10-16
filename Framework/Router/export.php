<?php
declare(strict_types=1);
namespace Framework\Router;

use Framework\ObjectManager\ObjectManager;
use Framework\EventManager\EventManagerInterface;
use Framework\Application\HttpApplication;
use Framework\Application\ConsoleApplication;

ObjectManager::getSingleton()->get(EventManagerInterface::class)
    ->addEventListener(
        HttpApplication::class,
        HttpApplication::TRIGGER_INITED,
        function () {
            ObjectManager::getSingleton()->export([
                RouterInterface::class => Http\Router::class,
            ]);
        }
    )
    ->addEventListener(
        ConsoleApplication::class,
        ConsoleApplication::TRIGGER_INITED,
        function () {
            ObjectManager::getSingleton()->export([
                RouterInterface::class => Console\Router::class,
            ]);
        }
    );
