<?php

namespace Framework\Repository;

use Framework\ObjectManager\ObjectManager;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;

trait EntityManagerAwareTrait
{
    private static $EntityManager;

    public function setEntityManager(DoctrineEntityManager $EntityManager)
    {
        static::$EntityManager = $EntityManager;
    }

    public function getEntityManager()
    {
        if (!static::$EntityManager) {
            $this->setEntityManager(ObjectManager::getSingleton()->get(EntityManager::class));
        }
        return static::$EntityManager;
    }
}
