<?php
namespace Framework\Authentication;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    SessionManager::class => SessionManagerFactory::class
]);
