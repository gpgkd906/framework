<?php
declare(strict_types=1);

namespace Framework\EventManager;

interface EventManagerAwareInterface
{
    public function setEventManager(EventManagerInterface $EventManager);
    public function getEventManager();
}
