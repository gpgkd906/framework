<?php

namespace Framework\ObjectManager;

trait ObjectManagerAwareTrait
{
    /**
    *
    * @api
    * @var mixed $objectManager
    * @access private
    * @link
    */
    private $objectManager = null;

    /**
    *
    * @api
    * @param mixed $objectManager
    * @return mixed $objectManager
    * @link
    */
    public function setObjectManager($objectManager)
    {
        return $this->objectManager = $objectManager;
    }

    /**
    *
    * @api
    * @return mixed $objectManager
    * @link
    */
    public function getObjectManager()
    {
        return $this->objectManager;
    }
}
