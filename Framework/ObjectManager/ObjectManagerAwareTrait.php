<?php
declare(strict_types=1);

namespace Framework\ObjectManager;

trait ObjectManagerAwareTrait
{
    /**
    *
    * @api
    * @var mixed $ObjectManager
    * @access private
    * @link
    */
    private static $ObjectManager = null;

    /**
    *
    * @api
    * @param mixed $ObjectManager
    * @return mixed $ObjectManager
    * @link
    */
    public function setObjectManager(ObjectManagerInterface $ObjectManager)
    {
        return self::$ObjectManager = $ObjectManager;
    }

    /**
    *
    * @api
    * @return mixed $ObjectManager
    * @link
    */
    public function getObjectManager()
    {
        return self::$ObjectManager;
    }
}
