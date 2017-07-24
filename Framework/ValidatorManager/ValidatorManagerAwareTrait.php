<?php

namespace Framework\ValidatorManager;

use Framework\ObjectManager\ObjectManager;

trait ValidatorManagerAwareTrait
{
    private static $ValidatorManager;

    public function setValidatorManager(ValidatorManagerInterface $ValidatorManager)
    {
        self::$ValidatorManager = $ValidatorManager;
    }

    public function getValidatorManager()
    {
        if (self::$ValidatorManager === null) {
            self::$ValidatorManager = ObjectManager::getSingleton()->get(ValidatorManagerInterface::class, ValidatorManager::class);
        }
        return self::$ValidatorManager;
    }
}
