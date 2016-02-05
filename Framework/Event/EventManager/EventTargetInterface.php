<?php

namespace Framework\Event\EventManager;

interface EventTargetInterface 
{
    const TRIGGER_INIT = "Initiation";
    const TRIGGER_INITED = "Initialized";
    const TRIGGER_DEINIT = "Deinitiation";
    
    public function addEventListener($eventName, $listener);
    
    public function removeEventListener($eventName, $listener);
    
    public function dispatchEvent(Event $event);
}
