<?php

namespace Framework\Core\Interfaces;

interface EventInterface 
{
    public function addEventListener($eventName, $callback);
    
    public function removeEventListener($eventName, $callback);
    
    public function triggerEvent($eventName);
}
