<?php
declare(strict_types=1);

namespace Framework\ModelManager;
use Framework\ObjectManager\ObjectManager;

trait ModelManagerAwareTrait
{
    private static $ModelManager;

    public function setModelManager(ModelManagerInterface $ModelManager)
    {
        self::$ModelManager = $ModelManager;
    }

    public function getModelManager()
    {
        if (!self::$ModelManager) {
            $this->setModelManager(ObjectManager::getSingleton()->get(ModelManagerInterface::class, ModelManager::class));
        }
        return self::$ModelManager;
    }
}
