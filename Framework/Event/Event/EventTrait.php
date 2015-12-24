<?php

namespace Framework\Event\Event;

use Exception;

trait EventTrait
{
    private $eventQueue = [];
    //
    private $triggerScope = [];
    private $propagationStopped = false;
    private $preventDefault = false;
    static private $eventTrigger = [];
    
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

    public function getCurrentEvent()
    {
        return $this->triggerScope[count($this->triggerScope) - 1];
    }

    static public function traceEvent()
    {
        print_r(self::$triggerScope);
    }

    public function triggerEvent($event, $parameters = [])
    {
        $trigger = $this->getTrigger($event);
        $oldPropagationStopped = $this->propagationStopped;
        $this->propagationStopped = false;
        $this->triggerFire($trigger, $parameters);
        foreach(EventManager::getPropagationChain(static::class) as $propagation) {
            if($this->propagationStopped) {
                break;
            }
            EventManager::triggerEvent($propagation, $event, $this, $parameters);
        }
        $this->propagationStopped = $oldPropagationStopped;
    }
    
    private function triggerFire($trigger, $parameters = [])
    {
        if(in_array($trigger, $this->triggerScope)) {
            throw new Exception(sprintf(EventInterface::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        $this->triggerScope[] = $trigger;
        if(!isset($this->eventQueue[$trigger])) {
            $this->eventQueue[$trigger] = [];
        }
        array_unshift($parameters, $this);
        $this->preventDefault = false;
        foreach($this->eventQueue[$trigger] as $key => $call) {
            if($this->preventDefault) {
                break;
            }
            call_user_func_array($call, $parameters);
        }
        array_pop($this->triggerScope);
    }
    
    private function getTrigger($event)
    {
        if($trigger = EventManager::getTrigger(static::class, $event)) {
            return $trigger;
        }
        throw new Exception(sprintf(EventInterface::ERROR_UNDEFINED_EVENT_TRIGGER, $event, static::class));
    }

    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    public function preventDefault()
    {
        $this->preventDefault = true;
    }

}
