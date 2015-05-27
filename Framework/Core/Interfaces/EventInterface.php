<?php

namespace Framework\Core\Interfaces;

interface EventInterface 
{
    const ERROR_EVENT_STACK_EXISTS = "error: triggered event [%s] was exists in class [%s]'s eventStack;";
    const EVENT_INVALID_CALLBACK_ADD_EVENT = "error: invalid callback with add event [%s]";
    const EVENT_INVALID_CALLBACK_REMOVE_EVENT = "error: invalid callback with remove event [%s]";
    
    public function addEventListener($eventName, $callback);
    
    public function removeEventListener($eventName, $callback);
    
    public function triggerEvent($eventName);
}
