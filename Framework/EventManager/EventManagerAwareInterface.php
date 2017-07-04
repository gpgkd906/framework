<?php

namespace Framework\EventManager;

interface EventManagerAwareInterface
{
    public function setEventManager(EventManagerInterface $EventManager);
    public function getEventManager();
}
