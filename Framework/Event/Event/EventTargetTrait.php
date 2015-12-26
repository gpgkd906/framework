<?php

namespace Framework\Event\Event;

trait EventTargetTrait
{

    public function addEventListener($event, $listener)
    {
        EventManager::addEventListener(static::class, $event, $listener);
    }
    
    public function removeEventListener($event, $listener)
    {
        EventManager::removeEventListener(static::class, $event, $listener);
    }

    public function dispatchEvent(Event $Event)
    {
        $Event->setTarget($this);
        foreach(EventManager::getPropagationChain(static::class) as $propagation) {
            if($Event->isBubbles() === false) {
                break;
            }
            EventManager::dispatchEvent($propagation, $Event);
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
        return EventManager::getCurrentEvent();
    }
}
