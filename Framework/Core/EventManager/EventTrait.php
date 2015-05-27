<?php

namespace Framework\Core\EventManager;
use Framework\Core\Interfaces\EventInterface;

trait EventTrait
{
    private $eventQueue = [];
    //
    private $eventStack = [];

    public function addEventListener($eventName, $callBack)
    {
        if(!isset($this->eventQueue[$eventName])) {
            $this->eventQueue[$eventName] = [];
        }
        if(!is_callable($callBack)) {
            throw new Exception(sprintf(EventInterface::EVENT_INVALID_CALLBACK_ADD_EVENT, $eventName));
        }
        $this->eventQueue[$eventName][] = $callBack;
    }

    public function removeEventListener($eventName, $callBack) {
        if(!isset($this->eventQueue[$eventName])) {
            $this->eventQueue[$eventName] = [];
        }
        if(!is_callable($callBack)) {
            throw new Exception(sprintf(EventInterface::EVENT_INVALID_CALLBACK_REMOVE_EVENT, $eventName));
        }
        foreach($this->eventQueue[$eventName] as $key => $call) {
            if($callBack == $call) {
                unset($this->eventQueue[$eventName][$key]);
                break;
            }
        }
    }

    public function triggerEvent($eventName, $parameters = [])
    {
        if(in_array($eventName, $this->eventStack)) {
            throw new Exception(sprintf(EventInterface::ERROR_EVENT_STACK_EXISTS, $eventName, get_class($this)));
        }
        $this->eventStack[] = $eventName;
        if(!isset($this->eventQueue[$eventName])) {
            $this->eventQueue[$eventName] = [];
        }
        array_unshift($parameters, $this);
        foreach($this->eventQueue[$eventName] as $key => $call) {
            $parameters = call_user_func_array($call, $parameters);
        }
        array_pop($this->eventStack);
    }

    public function getCurrentEvent()
    {
        return $this->eventStack[count($this->eventStack) - 1];
    }
}
