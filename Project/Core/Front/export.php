<?php
declare(strict_types=1);
namespace Project\Core\Front;

use Std\Controller\ControllerInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    ControllerInterface::class => Controller\NotFoundController::class
]);
