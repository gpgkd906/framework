<?php

namespace Framework\Event\Event;

class EventManager
{
    const ERROR_EVENT_STACK_EXISTS = "error: event [%s] is loop-triggered in class [%s]'s eventStack;";
    const ERROR_UNDEFINED_EVENT_TRIGGER = "error: undefiend event trigger [%s] in class [%s]";
    const ERROR_INVALID_CALLBACK_ADD_EVENT = "error: invalid callback with add event [%s]";
    const ERROR_INVALID_CALLBACK_REMOVE_EVENT = "error: invalid callback with remove event [%s]";
    const ERROR_LISTENERS_IS_ADDING_TO_EVENT_WHICH_IS_ADDED = 'error: LISTENERS_IS_ADDING_TO_EVENT_WHICH_IS_ADDED';
    
    static private $eventQueue = [];
    static private $triggerScope = [];
    static private $triggerPool = [];
    static private $propagationChainPool = [];
    
    static public function addEventListener($class, $event, $listener)
    {
        $trigger = self::getTrigger($class, $event);
        if(!isset(self::$eventQueue[$trigger])) {
            self::$eventQueue[$trigger] = [];
        }
        if(!is_callable($listener)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CALLBACK_ADD_EVENT, $trigger));
        }
        if(in_array($listener, self::$eventQueue[$trigger])) {
            throw new Exception(sprintf(self::ERROR_LISTENERS_IS_ADDING_TO_EVENT_WHICH_IS_ADDED, $trigger));
        }
        self::$eventQueue[$trigger][] = $listener;
    }
    
    static public function removeEventListener($class, $event, $listener)
    {
        $trigger = self::getTrigger($class, $event);
        if(!isset(self::$eventQueue[$trigger])) {
            return false;
        }
        if(!is_callable($listener)) {
            throw new Exception(sprintf(self::ERROR_INVALID_CALLBACK_REMOVE_EVENT, $trigger));
        }
        foreach(self::$eventQueue[$trigger] as $key => $call) {
            if($listener == $call) {
                unset(self::$eventQueue[$trigger][$key]);
                break;
            }
        }    
    }

    static public function getEventListeners($class, $event)
    {
        $trigger = self::getTrigger($class, $event);
        if(empty($trigger)) {
            return [];
        }
        if($class instanceof EventTargetInterface) {
            return $class->getEventListeners($event, $trigger);
        }
        if(!isset(self::$eventQueue[$trigger])) {
            self::$eventQueue[$trigger] = [];
        }
        return self::$eventQueue[$trigger];
    }
    
    static public function dispatchEvent($class, Event $Event)
    {
        $trigger = self::getTrigger($class, $Event);
        if(empty($trigger)) {
            return false;
        }
        if(!isset(self::$eventQueue[$trigger])) {
            return false;
        }
        if(in_array($Event, self::$triggerScope)) {
            throw new Exception(sprintf(self::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        self::$triggerScope[] = $Event;
        foreach(self::$eventQueue[$trigger] as $key => $call) {
            if($Event->isDefaultPrevented()) {
                break;
            }
            call_user_func($call, $Event);
        }
        array_pop(self::$triggerScope);
    }

    static public function dispatchTargetEvent($target, $class, Event $Event)
    {
        $trigger = self::getTrigger($class, $Event);
        if(empty($trigger)) {
            return false;
        }
        $eventListeners = $target->getEventListeners($Event->getName(), $trigger);
        if(empty($eventListeners)) {
            return false;
        }
        if(in_array($Event, self::$triggerScope)) {
            throw new Exception(sprintf(self::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        self::$triggerScope[] = $Event;
        foreach($eventListeners as $key => $call) {
            if($Event->isDefaultPrevented()) {
                break;
            }
            call_user_func($call, $Event);
        }
        array_pop(self::$triggerScope);
    }    

    static public function getCurrentEvent()
    {
        return self::$triggerScope[count(self::$triggerScope) - 1] ?? null;
    }
    
    static public function traceEvent()
    {        
        print_r(self::$triggerScope);
    }    

    static public function getTrigger($class, $event)
    {
        if($event instanceof Event) {
            $event = $event->getName();
        }
        $triggerPool = self::initTrigger($class);
        if(isset($triggerPool[$event])) {
            return $triggerPool[$event];
        }
    }

    static public function initTrigger($class)
    {
        if(!isset(self::$triggerPool[$class])) {
            $reflection = new \ReflectionClass($class);
            $eventTrigger = [];
            foreach($reflection->getConstants() as $constantName => $val) {
                //TRIGGER_が始まるトリッガを拾う
                if(strpos($constantName, 'TRIGGER_') === 0) {
                    //クラス情報をトリッガにセットする
                    $eventTrigger[$val] = $class . "::" . $val;
                }
            }
            self::$triggerPool[$class] = $eventTrigger;
        }
        return self::$triggerPool[$class];
    }

    static public function getPropagationChain($class)
    {
        if(!isset(self::$propagationChainPool[$class])) {
            self::$propagationChainPool[$class] = [$class] + class_parents($class) + class_implements($class);
        }
        return self::$propagationChainPool[$class];
    }

    static public function createEvent($name)
    {
        return new Event($name);
    }
}