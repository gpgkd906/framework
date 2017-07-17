<?php
namespace Framework\Service;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    SessionManager::class => SessionManagerFactory::class
]);
