<?php

namespace Framework\Repository\Doctrine;

use Doctrine\ORM\EntityManager;

trait EntityManagerAwareTrait
{
    private $EntityManager;

    public function setEntityManager(EntityManager $EntityManager)
    {
        $this->EntityManager = $EntityManager;
    }

    public function getEntityManager()
    {
        return $this->EntityManager;
    }
}
