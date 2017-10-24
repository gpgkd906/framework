<?php
declare(strict_types=1);
namespace Std\Repository;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()->export([
    EntityManager::class => Doctrine\EntityManagerFactory::class,
]);
