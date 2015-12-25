<?php

namespace Framework\Event\Event;

class EventManager
{
    static private $eventQueue = [];
    static private $triggerScope = [];
    static private $triggerPool = [];
    static private $propagationChainPool = [];
    
    static public function addEventListener($class, $event, $callBack)
    {
        $trigger = self::getTrigger($class, $event);
        if(!isset(self::$eventQueue[$trigger])) {
            self::$eventQueue[$trigger] = [];
        }
        if(!is_callable($callBack)) {
            throw new Exception(sprintf(EventTargetInterface::ERROR_INVALID_CALLBACK_ADD_EVENT, $trigger));
        }
        self::$eventQueue[$trigger][] = $callBack;
    }
    
    static public function removeEventListener($class, $event, $callBack)
    {
        $trigger = self::getTrigger($class, $event);
        if(!isset(self::$eventQueue[$trigger])) {
            return false;
        }
        if(!is_callable($callBack)) {
            throw new Exception(sprintf(EventTargetInterface::ERROR_INVALID_CALLBACK_REMOVE_EVENT, $trigger));
        }
        foreach(self::$eventQueue[$trigger] as $key => $call) {
            if($callBack == $call) {
                unset(self::$eventQueue[$trigger][$key]);
                break;
            }
        }        
    }
    
    static public function triggerEvent($class, Event $Event, $parameters = [])
    {
        $trigger = self::getTrigger($class, $Event);
        if(empty($trigger)) {
            return false;
        }
        if(!isset(self::$eventQueue[$trigger])) {
            return false;
        }
        if(in_array($Event, self::$triggerScope)) {
            throw new Exception(sprintf(EventTargetInterface::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        self::$triggerScope[] = $Event;
        array_unshift($parameters, $Event);
        foreach(self::$eventQueue[$trigger] as $key => $call) {
            call_user_func_array($call, $parameters);
        }
        array_pop(self::$triggerScope);
    }

    public function getCurrentEvent()
    {
        return self::$triggerScope[count(self::$triggerScope) - 1] ?? null;
    }
    
    public function traceEvent()
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