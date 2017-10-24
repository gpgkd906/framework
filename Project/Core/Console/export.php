<?php
declare(strict_types=1);
namespace Project\Core\Console;

use Std\Controller\ConsoleInterface;
use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    ConsoleInterface::class => Controller\HelpController::class
]);
