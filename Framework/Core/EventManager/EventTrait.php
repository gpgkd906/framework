<?php

namespace Framework\Core\EventManager;
use Framework\Core\Interfaces\EventInterface;

trait EventTrait
{
    private $eventQueue = [];
    //
    private $eventStack = [];

    public function addEventListener($event, $callBack)
    {
        $trigger = $this->getTrigger($event);
        if(!isset($this->eventQueue[$trigger])) {
            $this->eventQueue[$trigger] = [];
        }
        if(!is_callable($callBack)) {
            throw new Exception(sprintf(EventInterface::ERROR_INVALID_CALLBACK_ADD_EVENT, $trigger));
        }
        $this->eventQueue[$trigger][] = $callBack;
    }

    public function removeEventListener($event, $callBack)
    {
        $trigger = $this->getTrigger($event);
        if(!isset($this->eventQueue[$trigger])) {
            $this->eventQueue[$trigger] = [];
        }
        if(!is_callable($callBack)) {
            throw new Exception(sprintf(EventInterface::ERROR_INVALID_CALLBACK_REMOVE_EVENT, $trigger));
        }
        foreach($this->eventQueue[$trigger] as $key => $call) {
            if($callBack == $call) {
                unset($this->eventQueue[$trigger][$key]);
                break;
            }
        }
    }

    private function triggerFire($trigger, $parameters = [])
    {
        if(in_array($trigger, $this->eventStack)) {
            throw new Exception(sprintf(EventInterface::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        $this->eventStack[] = $trigger;
        if(!isset($this->eventQueue[$trigger])) {
            $this->eventQueue[$trigger] = [];
        }
        array_unshift($parameters, $this);

        foreach($this->eventQueue[$trigger] as $key => $call) {
            $parameters = call_user_func_array($call, $parameters);
        }
        array_pop($this->eventStack);
        return $parameters;
    }

    public function getCurrentEvent()
    {
        return $this->eventStack[count($this->eventStack) - 1];
    }

    private function getTrigger($event)
    {
        if(isset($this->eventTrigger) && isset($this->eventTrigger[$event])) {
            return $this->eventTrigger[$event];
        }
        throw new Exception(sprintf(EventInterface::ERROR_UNDEFINED_EVENT_TRIGGER, $eventName, get_class($this)));        
    }

    public function triggerEvent($event, $parameters = [])
    {
        $trigger = $this->getTrigger($event);
        return $this->triggerFire($trigger, $parameters);
    }
    
    public function initTrigger()
    {
        if(isset($this->eventTrigger)) {
            $classLabel = get_class($this);
            foreach($this->eventTrigger as $key => $trigger) {
                if($trigger === null) {
                    $this->eventTrigger[$key] = $classLabel . "\\" . $key;
                }
            }
        }
    }
}
