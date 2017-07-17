<?php

namespace Framework\Repository;

use Doctrine\ORM\EntityManager;

interface EntityManagerAwareInterface
{
    public function setEntityManager(EntityManager $EntityManager);
    public function getEntityManager();
}
