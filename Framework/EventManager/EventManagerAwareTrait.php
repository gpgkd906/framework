<?php

namespace Framework\EventManager;

trait EventManagerAwareTrait
{
    private $EventManager = null;

    public function setEventManager(EventManagerInterface $EventManager)
    {
        $this->EventManager = $EventManager;
    }

    public function getEventManager()
    {
        if ($this->EventManager === null) {
            $this->EventManager = EventManager::getSingleton();
        }
        return $this->EventManager;
    }
}
