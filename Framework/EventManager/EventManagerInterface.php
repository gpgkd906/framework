<?php
declare(strict_types=1);

namespace Framework\EventManager;

interface EventManagerInterface
{
    public function addEventListener($class, $event, callable $listener);

    public function removeEventListener($class, $event, callable $listener);

    public function getEventListeners($class, $event);

    public function dispatchEvent($class, Event $Event);

    public function dispatchTargetEvent(EventTargetInterface $target, $targetClass, Event $Event);

    public function createEvent($name);
}
