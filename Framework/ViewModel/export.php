<?php
declare(strict_types=1);
namespace Framework\ViewModel;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    ViewModelManagerInterface::class => ViewModelManager::class,
]);
