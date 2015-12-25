<?php

namespace Framework\Event\Event;

use Exception;

trait EventTargetTrait
{
    private $eventListeners = [];
    private $triggerScope = [];
    
    public function addEventListener($event, $listener)
    {
        $trigger = $this->getTrigger($event);
        if(!isset($this->eventListeners[$trigger])) {
            $this->eventListeners[$trigger] = [];
        }
        if(!is_callable($listener)) {
            throw new Exception(sprintf(EventTargetInterface::ERROR_INVALID_CALLBACK_ADD_EVENT, $trigger));
        }
        $this->eventListeners[$trigger][] = $listener;
    }
    
    public function removeEventListener($event, $listener)
    {
        $trigger = $this->getTrigger($event);
        if(!isset($this->eventListeners[$trigger])) {
            $this->eventListeners[$trigger] = [];
        }
        if(!is_callable($listener)) {
            throw new Exception(sprintf(EventTargetInterface::ERROR_INVALID_CALLBACK_REMOVE_EVENT, $trigger));
        }
        foreach($this->eventListeners[$trigger] as $key => $call) {
            if($listener == $call) {
                unset($this->eventListeners[$trigger][$key]);
                break;
            }
        }
    }

    public function dispatchEvent(Event $Event)
    {
        $Event->setTarget($this);
        $parameters = $Event->getData();
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
        $Event->setData($parameters);
        $this->dispatchEvent($Event);
    }

    public function getCurrentEvent()
    {
        return $this->triggerScope[count($this->triggerScope) - 1] ?? EventManager::getCurrentEvent();
    }

    public function traceEvent()
    {
        print_r($this->triggerScope);
    }    
    
    private function triggerFire(Event $Event, $parameters = [])
    {
        $trigger = $this->getTrigger($Event);
        if(in_array($Event, $this->triggerScope)) {
            throw new Exception(sprintf(EventTargetInterface::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        $this->triggerScope[] = $Event;
        if(!isset($this->eventListeners[$trigger])) {
            array_pop($this->triggerScope);
            return false;
        }
        array_unshift($parameters, $Event);
        foreach($this->eventListeners[$trigger] as $key => $call) {
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
        throw new Exception(sprintf(EventTargetInterface::ERROR_UNDEFINED_EVENT_TRIGGER, $event, static::class));
    }
}
