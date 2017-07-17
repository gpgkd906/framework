<?php

namespace Framework\Repository\Doctrine;

interface EntityManagerAwareInterface
{
    public function setEntityManager(EntityManagerInterface $EntityManager);
    public function getEntityManager();
}
