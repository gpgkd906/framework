<?php

namespace Framework\EventManager;

use Framework\ObjectManager\SingletonInterface;

class EventManager implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    const ERROR_EVENT_STACK_EXISTS = "error: event [%s] is loop-triggered in class [%s]'s eventStack;";
    const ERROR_UNDEFINED_EVENT_TRIGGER = "error: undefiend event trigger [%s] in class [%s]";
    const ERROR_INVALID_CALLBACK_ADD_EVENT = "error: invalid callback with add event [%s]";
    const ERROR_INVALID_CALLBACK_REMOVE_EVENT = "error: invalid callback with remove event [%s]";
    const ERROR_LISTENERS_IS_ADDING_TO_EVENT_WHICH_IS_ADDED = 'error: LISTENERS_IS_ADDING_TO_EVENT_WHICH_IS_ADDED';

    private $eventQueue = [];
    private $triggerScope = [];
    private $triggerPool = [];
    private $propagationChainPool = [];

    public function addEventListener($class, $event, callable $listener)
    {
        $trigger = $this->getTrigger($class, $event);
        if (!isset($this->eventQueue[$trigger])) {
            $this->eventQueue[$trigger] = [];
        }
        if (in_array($listener, $this->eventQueue[$trigger])) {
            throw new Exception(sprintf(self::ERROR_LISTENERS_IS_ADDING_TO_EVENT_WHICH_IS_ADDED, $trigger));
        }
        $this->eventQueue[$trigger][] = $listener;
    }

    public function removeEventListener($class, $event, callable $listener)
    {
        $trigger = $this->getTrigger($class, $event);
        if (!isset($this->eventQueue[$trigger])) {
            return false;
        }
        foreach ($this->eventQueue[$trigger] as $key => $call) {
            if ($listener == $call) {
                unset($this->eventQueue[$trigger][$key]);
                break;
            }
        }
    }

    public function getEventListeners($class, $event)
    {
        if ($class instanceof EventTargetInterface) {
            return $class->getEventListeners($event);
        }
        $trigger = $this->getTrigger($class, $event);
        if (empty($trigger)) {
            return [];
        }
        if (!isset($this->eventQueue[$trigger])) {
            $this->eventQueue[$trigger] = [];
        }
        return $this->eventQueue[$trigger];
    }

    public function dispatchEvent($class, Event $Event)
    {
        if (in_array($Event, $this->triggerScope)) {
            throw new Exception(sprintf(self::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        $this->triggerScope[] = $Event;
        foreach (self::getPropagationChain($class) as $propagation) {
            if ($Event->isBubbles() === false) {
                break;
            }
            $trigger = $this->getTrigger($propagation, $Event);
            if (empty($trigger)) {
                continue;
            }
            if (!isset($this->eventQueue[$trigger])) {
                continue;
            }
            foreach ($this->eventQueue[$trigger] as $key => $call) {
                if ($Event->isDefaultPrevented()) {
                    $Event->resetDefaultPrevent();
                    break;
                }
                call_user_func($call, $Event);
            }
        }
        return array_pop($this->triggerScope);
    }

    public function dispatchTargetEvent(EventTargetInterface $target, $targetClass, Event $Event)
    {
        if (in_array($Event, $this->triggerScope)) {
            throw new Exception(sprintf(self::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        $this->triggerScope[] = $Event;
        $trigger = $this->getTrigger($targetClass, $Event);
        if (!empty($trigger)) {
            $eventListeners = $target->getEventListeners($Event->getName(), $trigger);
            if (!empty($eventListeners)) {
                foreach ($eventListeners as $key => $call) {
                    if ($Event->isDefaultPrevented()) {
                        $Event->resetDefaultPrevent();
                        break;
                    }
                    call_user_func($call, $Event);
                }
            }
        }
        array_pop($this->triggerScope);
        return $this->dispatchEvent($targetClass, $Event);
    }

    public function getCurrentEvent()
    {
        $triggerScopeLength = count($this->triggerScope) - 1;
        return $this->triggerScope[$triggerScopeLength] ? $this->triggerScope[$triggerScopeLength] : null;
    }

    public function traceEvent()
    {
        print_r($this->triggerScope);
    }

    public function getTrigger($class, $event)
    {
        if ($event instanceof Event) {
            $event = $event->getName();
        }
        $triggerPool = $this->initTrigger($class);
        if (isset($triggerPool[$event])) {
            return $triggerPool[$event];
        }
    }

    public function initTrigger($class)
    {
        if (!isset($this->triggerPool[$class])) {
            $reflection = new \ReflectionClass($class);
            $eventTrigger = [];
            foreach ($reflection->getConstants() as $constantName => $val) {
                //TRIGGER_が始まるトリッガを拾う
                if (strpos($constantName, 'TRIGGER_') === 0) {
                    //クラス情報をトリッガにセットする
                    $eventTrigger[$val] = $class . "::" . $val;
                }
            }
            $this->triggerPool[$class] = $eventTrigger;
        }
        return $this->triggerPool[$class];
    }

    public function getPropagationChain($class)
    {
        if (!isset($this->propagationChainPool[$class])) {
            $this->propagationChainPool[$class] = [$class] + class_parents($class) + class_implements($class);
        }
        return $this->propagationChainPool[$class];
    }

    public function createEvent($name)
    {
        return new Event($name);
    }
}
