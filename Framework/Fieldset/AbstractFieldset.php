<?php

namespace Framework\Fieldset;

use Framework\Repository\Repository\EntityInterface;
use Framework\EventManager\EventTargetInterface;
use Form2\Fieldset;

abstract class AbstractFieldset extends Fieldset implements EventTargetInterface
{
    use \Framework\EventManager\EventTargetTrait;
    
    CONST TRIGGER_SUBMIT = 'submit';
    
    private $bindEntity = null;
    
    /**
     * 生成した要素をアクセスする
     * @param string $name 要素名
     */
	public function get($name) {
		if(isset($this->elements[$name])) {
			return $this->elements[$name];
		}
	}

    public function __get($name)
    {
        return $this->get($name);
    }

    public function bind($entity)
    {
        if(!$entity instanceof EntityInterface) {
            return $this->bindData($entity);
        }
        $this->bindEntity = $entity;
        if($this->getData()) {
            $data = $this->getData();
            $this->bindEntity->propertyWalk(function ($property, $value) use ($data) {
                if(isset($data[$property])) {
                    return $data;
                }
            });
        } else {
            $this->bindEntity->propertyWalk(function ($property, $value) {
                if(isset($this->elements[$property])) {
                    $this->elements[$property]->set('value', $value);
                }
            });
        }
    }
    
    private function bindData($data)
    {        
        foreach($data as $name => $value) {
            if(isset($this->elements[$name])) {
                $this->elements[$name]->value($value);
            }
        }
    }

    public function onSubmit()
    {
        $this->triggerEvent(self::TRIGGER_SUBMIT, $this->getData());
    }
}
