<?php
namespace Framework\EventManager;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    EventManagerInterface::class => EventManager::class,
]);
