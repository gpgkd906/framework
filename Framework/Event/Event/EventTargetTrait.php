<?php

namespace Framework\Event\Event;

use Exception;

trait EventTargetTrait
{
    private $eventQueue = [];
    //
    private $triggerScope = [];
    private $propagationStopped = false;
    private $preventEvent = false;
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
        return $this->triggerScope[count($this->triggerScope) - 1] ?? EventManager::getCurrentEvent();
    }

    public function traceEvent()
    {
        print_r($this->triggerScope);
    }

    private function dispatchEvent(Event $Event, $parameters = [])
    {
        $Event->setTarget($this);
        $this->triggerFire($Event, $parameters);
        foreach(EventManager::getPropagationChain(static::class) as $propagation) {
            if($Event->isBubbles() === false) {
                break;
            }
            EventManager::triggerEvent($propagation, $Event, $parameters);
        }
    }

    public function triggerEvent($event, $parameters = [])
    {
        $Event = EventManager::createEvent($event);
        $this->dispatchEvent($Event, $parameters);
    }
    
    private function triggerFire(Event $Event, $parameters = [])
    {
        $trigger = $this->getTrigger($Event);
        if(in_array($Event, $this->triggerScope)) {
            throw new Exception(sprintf(EventInterface::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        $this->triggerScope[] = $Event;
        if(!isset($this->eventQueue[$trigger])) {
            array_pop($this->triggerScope);
            return false;
        }
        array_unshift($parameters, $Event);
        foreach($this->eventQueue[$trigger] as $key => $call) {
            if($Event->isDefaultPrevented()) {
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

    public function preventEvent()
    {
        $this->preventEvent = true;
    }

    public function stopImmediatePropagation()
    {
        $this->preventEvent = true;
        $this->propagationStopped = true;
    }
}
