<?php

namespace Framework\EventManager;

trait EventTargetTrait
{
    use EventManagerAwareTrait;
    private $eventListeners = [];
    private $triggerScope = [];

    public function addEventListener($event, callable $listener)
    {
        $trigger = $this->getTrigger($event);
        if (!isset($this->eventListeners[$trigger])) {
            $this->eventListeners[$trigger] = [];
        }
        $this->eventListeners[$trigger][] = $listener;
    }

    public function removeEventListener($event, callable $listener)
    {
        $trigger = $this->getTrigger($event);
        if (!isset($this->eventListeners[$trigger])) {
            $this->eventListeners[$trigger] = [];
        }
        foreach ($this->eventListeners[$trigger] as $key => $call) {
            if ($listener == $call) {
                unset($this->eventListeners[$trigger][$key]);
                break;
            }
        }
        $this->getEventManager()->removeEventListener($this, $event, $listener);
    }

    public function getEventListeners($event, $trigger = null)
    {
        $trigger = $trigger ? $trigger : $this->getTrigger($event);
        if (empty($trigger)) {
            return [];
        }
        if (!isset($this->eventListeners[$trigger])) {
            $this->eventListeners[$trigger] = [];
        }
        return $this->eventListeners[$trigger];
    }

    public function dispatchEvent(Event $Event)
    {
        $Event->setTarget($this);
        $this->getEventManager()->dispatchTargetEvent($this, static::class, $Event);
    }

    public function triggerEvent($event, $parameters = [])
    {
        $Event = $this->getEventManager()->createEvent($event);
        $Event->setData($parameters);
        $this->dispatchEvent($Event);
    }

    public function getCurrentEvent()
    {
        return $this->getEventManager()->getCurrentEvent();
    }

    private function getTrigger($event)
    {
        if ($trigger = $this->getEventManager()->getTrigger(static::class, $event)) {
            return $trigger;
        }
        throw new Exception(sprintf(EventTargetInterface::ERROR_UNDEFINED_EVENT_TRIGGER, $event, static::class));
    }
}
