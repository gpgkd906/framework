<?php
declare(strict_types=1);
namespace Framework\Module\Cngo\Front;

use Framework\Controller\ControllerInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    ControllerInterface::class => Controller\NotFoundController::class
]);
