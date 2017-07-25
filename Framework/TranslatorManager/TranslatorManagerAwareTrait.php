<?php

namespace Framework\TranslatorManager;

use Framework\ObjectManager\ObjectManager;

trait TranslatorManagerAwareTrait
{
    private static $TranslatorManager;

    public function setTranslatorManager(TranslatorManagerInterface $TranslatorManager)
    {
        self::$TranslatorManager = $TranslatorManager;
    }

    public function getTranslatorManager()
    {
        if (self::$TranslatorManager === null) {
            self::$TranslatorManager = ObjectManager::getSingleton()->get(TranslatorManagerInterface::class, TranslatorManager::class);
        }
        return self::$TranslatorManager;
    }
}
