<?php
declare(strict_types=1);

namespace Framework\EventManager;

trait EventManagerAwareTrait
{
    private static $EventManager = null;

    public function setEventManager(EventManagerInterface $EventManager)
    {
        self::$EventManager = $EventManager;
    }

    public function getEventManager()
    {
        if (self::$EventManager === null) {
            self::$EventManager = EventManager::getSingleton();
        }
        return self::$EventManager;
    }
}
