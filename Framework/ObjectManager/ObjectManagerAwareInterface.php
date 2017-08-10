<?php
declare(strict_types=1);

namespace Framework\ObjectManager;

interface ObjectManagerAwareInterface
{
    public function setObjectManager(ObjectManagerInterface $objectManager);

    public function getObjectManager();
}
