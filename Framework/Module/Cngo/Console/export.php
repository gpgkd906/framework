<?php
declare(strict_types=1);
namespace Framework\Module\Cngo\Console;

use Framework\Controller\ConsoleInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    ConsoleInterface::class => Controller\HelpController::class
]);
