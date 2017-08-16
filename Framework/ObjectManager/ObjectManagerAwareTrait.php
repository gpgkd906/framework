<?php
/**
 * PHP version 7
 * File ObjectManagerAwareTrait.php
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ObjectManager;

/**
 * Trait ObjectManagerAwareTrait
 * 
 * @category Trait
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait ObjectManagerAwareTrait
{
    private static $_ObjectManager = null;

    /**
     * Method setObjectMananger
     *
     * @param ObjectManagerInterface $ObjectManager ObjectManager
     * 
     * @return void
     */
    public function setObjectManager(ObjectManagerInterface $ObjectManager)
    {
        return self::$_ObjectManager = $ObjectManager;
    }

    /**
     * Method getObjectManager
     *
     * @return ObjectManagerInterface $ObjectManager
     */
    public function getObjectManager()
    {
        return self::$_ObjectManager;
    }
}
