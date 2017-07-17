<?php
namespace Framework\Repository;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    'EntityManager' => DoctrineFactory::class,
    EntityManager::class => DoctrineFactory::class,
]);
