<?php

namespace Framework\Event\Event;

class Event
{
    private $name = null;
    private $data = null;    
    private $target = null;
    private $defaultPrevented = false;
    private $bubbles = true;
    private $cancelable = true;

    public function __construct($name, $config = [])
    {
        $this->name = $name;
        $this->cancelable = $config['cancelable'] ?? true;
        $this->bubbles = $config['bubbles'] ?? true;
    }
    
    public function getName ()
    {
        return $this->name;
    }

    public function setData ($data)
    {
        return $this->data = $data;
    }

    public function getData ()
    {
        return $this->data;
    }

    public function setTarget ($target)
    {
        return $this->target = $target;
    }

    public function getTarget ()
    {
        return $this->target;
    }
    
    public function isDefaultPrevented()
    {
        return $this->defaultPrevented;
    }

    public function isBubbles()
    {
        return $this->bubbles;
    }

    public function preventDefault()
    {
        if($this->cancelable) {
            $this->defaultPrevented = true;
        }
    }

    public function StopPropagation()
    {
        if($this->cancelable) {
            $this->bubbles = false;
        }
    }
}
