<?php
namespace Framework\Module\Cngo\Console;

use Framework\Controller\ControllerInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    ControllerInterface::class => Controller\ListController::class
]);
