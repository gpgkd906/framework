<?php

namespace Framework\Event\EventManager;

trait EventTargetTrait
{
    private $eventListeners = [];
    private $triggerScope = [];
 
    public function addEventListener($event, $listener)
    {
        $trigger = self::getTrigger($event);
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
        $trigger = self::getTrigger($event);
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
        EventManager::removeEventListener($this, $event, $listener);
    }

    public function getEventListeners($event, $trigger = null)
    {
        $trigger = $trigger ?? self::getTrigger($event);
        if(empty($trigger)) {
            return [];
        }
        if(!isset($this->eventListeners[$trigger])) {
            $this->eventListeners[$trigger] = [];
        }
        return $this->eventListeners[$trigger];
    }

    public function dispatchEvent(Event $Event)
    {
        $Event->setTarget($this);
        EventManager::dispatchTargetEvent($this, static::class, $Event);
    }
    
    public function triggerEvent($event, $parameters = [])
    {
        $Event = EventManager::createEvent($event);
        $Event->setData($parameters);
        $this->dispatchEvent($Event);
    }

    public function getCurrentEvent()
    {
        return EventManager::getCurrentEvent();
    }

    static private function getTrigger($event)
    {
        if($trigger = EventManager::getTrigger(static::class, $event)) {
            return $trigger;
        }
        throw new Exception(sprintf(EventTargetInterface::ERROR_UNDEFINED_EVENT_TRIGGER, $event, static::class));
    }
}
