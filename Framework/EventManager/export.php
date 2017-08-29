<?php
// @codingStandardsIgnoreFile
declare(strict_types=1);
namespace Framework\EventManager;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    EventManagerInterface::class => EventManager::class,
]);
