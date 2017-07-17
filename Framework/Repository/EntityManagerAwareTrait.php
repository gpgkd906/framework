<?php

namespace Framework\Repository;

use Framework\ObjectManager\ObjectManager;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;

trait EntityManagerAwareTrait
{
    private static $EntityManager;

    public function setEntityManager(DoctrineEntityManager $EntityManager)
    {
        self::$EntityManager = $EntityManager;
    }

    public function getEntityManager()
    {
        if (!self::$EntityManager) {
            $this->setEntityManager(ObjectManager::getSingleton()->get(EntityManager::class));
        }
        return self::$EntityManager;
    }
}
