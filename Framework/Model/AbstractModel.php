<?php

namespace Framework\Model;

use Framework\Application\ServiceManagerAwareInterface;
use Framework\Application\SingletonInterface;
    
Abstract class AbstractModel implements ModelInterface, ServiceManagerAwareInterface, SingletonInterface
{
    use \Framework\Application\ServiceManagerAwareTrait;
    use \Framework\Application\SingletonTrait;

    abstract public function getEntities();
}