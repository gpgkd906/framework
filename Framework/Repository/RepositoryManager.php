<?php

namespace Framework\Repository;

use Framework\ObjectManager\SingletonInterface;

class RepositoryManager implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $entityPath = [];

    public function addEntityPath($path)
    {
        $this->entityPath[] = $path;
    }

    public function getEntityPath()
    {
        return $this->entityPath;
    }
}
