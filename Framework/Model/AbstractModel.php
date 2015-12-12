<?php

namespace Framework\Model;

use Framework\Application\ServiceManagerAwareInterface;
    
Abstract class AbstractModel implements ModelInterface, ServiceManagerAwareInterface
{
    use \Framework\Application\ServiceManagerAwareTrait;
    use \Framework\Application\SingletonTrait;

    abstract public function getEntities();
}