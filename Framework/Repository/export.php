<?php
namespace Framework\Repository;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    EntityManager::class => Doctrine\EntityManagerFactory::class,
]);
