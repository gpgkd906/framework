<?php

namespace Framework\ObjectManager;

interface ObjectManagerAwareInterface
{
    public function setObjectManager(ObjectManagerInterface $objectManager);

    public function getObjectManager();
}
