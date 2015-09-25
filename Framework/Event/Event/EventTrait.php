<?php

namespace Framework\Event\Event;

use Exception;

trait EventTrait
{
    private $eventQueue = [];
    //
    private $eventStack = [];

    private $eventTrigger = null;

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
        if(in_array($trigger, $this->eventStack)) {
            throw new Exception(sprintf(EventInterface::ERROR_EVENT_STACK_EXISTS, $trigger));
        }
        $this->eventStack[] = $trigger;
        if(!isset($this->eventQueue[$trigger])) {
            $this->eventQueue[$trigger] = [];
        }
        array_unshift($parameters, $this);

        foreach($this->eventQueue[$trigger] as $key => $call) {
            $parameters = call_user_func_array($call, $parameters);
        }
        array_pop($this->eventStack);
        return $parameters;
    }

    public function getCurrentEvent()
    {
        return $this->eventStack[count($this->eventStack) - 1];
    }

    private function getTrigger($event)
    {
        $this->initTrigger();
        if(isset($this->eventTrigger) && isset($this->eventTrigger[$event])) {
            return $this->eventTrigger[$event];
        }
        throw new Exception(sprintf(EventInterface::ERROR_UNDEFINED_EVENT_TRIGGER, $event, get_class($this)));        
    }

    public function triggerEvent($event, $parameters = [])
    {
        $trigger = $this->getTrigger($event);
        return $this->triggerFire($trigger, $parameters);
    }
    
    private function initTrigger()
    {
        if(empty($this->eventTrigger)) {
            $reflection = new \ReflectionClass($this);
            $reflection->getConstants();
            $classLabel = $reflection->getName();
            foreach($reflection->getConstants() as $constantName => $val) {
                //TRIGGER_が始まるトリッガを拾う
                if(strpos($constantName, 'TRIGGER_') === 0) {
                    //クラス情報をトリッガにセットする
                    $this->eventTrigger[$val] = $classLabel . "\\" . $val;
                }
            }
        }
    }
}
