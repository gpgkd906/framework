<?php

namespace Framework\Core;

trait EventTrait
{
    private $eventQueue = [];

    public function addEventListener($eventName, $callBack)
    {
        if(!isset($this->eventQueue[$eventName])) {
            $this->eventQueue[$eventName] = [];
        }
        if(!is_callable($callBack)) {
            throw new Exception("error: EVENT_INVALID_CALLBACK");
        }
        $this->eventQueue[$eventName][] = $callBack;
    }

    public function removeEventListener($eventName, $callBack) {
        if(!isset($this->eventQueue[$eventName])) {
            $this->eventQueue[$eventName] = [];
        }
        if(!is_callable($callBack)) {
            throw new Exception("error: EVENT_INVALID_CALLBACK");
        }
        foreach($this->eventQueue[$eventName] as $key => $call) {
            if($callBack == $call) {
                unset($this->eventQueue[$eventName][$key]);
                break;
            }
        }
    }

    public function triggerEvent($eventName)
    {
        if(!isset($this->eventQueue[$eventName])) {
            $this->eventQueue[$eventName] = [];
        }
        foreach($this->eventQueue[$eventName] as $key => $call) {
            call_user_func_array($call, [$this]);
        }       
    }
}
