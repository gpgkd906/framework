<?php

namespace Framework\Core\Interfaces;

interface EventInterface 
{
    const ERROR_EVENT_STACK_EXISTS = "error: event [%s] is loop-triggered in class [%s]'s eventStack;";
    const ERROR_UNDEFINED_EVENT_TRIGGER = "error: undefiend event trigger [%s] in class [%s]";
    const ERROR_INVALID_CALLBACK_ADD_EVENT = "error: invalid callback with add event [%s]";
    const ERROR_INVALID_CALLBACK_REMOVE_EVENT = "error: invalid callback with remove event [%s]";

    const TRIGGER_INIT = "Initiation";
    const TRIGGER_INITED = "Initialized";
    const TRIGGER_DEINIT = "Deinitiation";
    
    public function addEventListener($eventName, $callback);
    
    public function removeEventListener($eventName, $callback);
    
    public function triggerEvent($eventName);
}
