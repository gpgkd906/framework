<?php

namespace Framework\Event\Event;

use Exception;

trait EventTrait
{
    private $eventQueue = [];
    //
    private $triggerScope = [];
    
    static private $triggerStack = [];

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
        foreach($this->eventQueue[$trigger] as $key => $call) {
            call_user_func_array($call, $parameters);
        }
        array_pop($this->triggerScope);
        return $parameters;
    }

    public function getCurrentEvent()
    {
        return $this->triggerScope[count($this->triggerScope) - 1];
    }

    private function getTrigger($event)
    {
        $this->initTrigger();
        if(isset(self::$eventTrigger[static::class]) && isset(self::$eventTrigger[static::class][$event])) {
            return self::$eventTrigger[static::class][$event];
        }
        throw new Exception(sprintf(EventInterface::ERROR_UNDEFINED_EVENT_TRIGGER, $event, static::class));
    }

    public function triggerEvent($event, $parameters = [])
    {
        $trigger = $this->getTrigger($event);
        self::$triggerStack[] = $trigger;
        return $this->triggerFire($trigger, $parameters);
    }
    
    private function initTrigger()
    {
        if(empty(self::$eventTrigger[static::class])) {
            $eventTrigger = [];
            $reflection = new \ReflectionClass($this);
            $classLabel = static::class;
            foreach($reflection->getConstants() as $constantName => $val) {
                //TRIGGER_が始まるトリッガを拾う
                if(strpos($constantName, 'TRIGGER_') === 0) {
                    //クラス情報をトリッガにセットする
                    $eventTrigger[$val] = $classLabel . "::" . $val;
                }
            }
            self::$eventTrigger[static::class] = $eventTrigger;
        }
    }

    static public function traceEvent()
    {
        print_r(self::$triggerStack);
    }
}
