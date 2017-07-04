<?php

namespace Framework\EventManager;

interface EventTargetInterface
{
    const TRIGGER_INIT = "Initiation";
    const TRIGGER_INITED = "Initialized";
    const TRIGGER_DEINIT = "Deinitiation";

    public function addEventListener($eventName, callable $listener);

    public function removeEventListener($eventName, callable $listener);

    public function dispatchEvent(Event $event);
}
