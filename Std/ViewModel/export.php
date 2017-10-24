<?php
declare(strict_types=1);
namespace Std\ViewModel;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    ViewModelManagerInterface::class => ViewModelManager::class,
]);
