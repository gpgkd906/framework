<?php
/**
 * PHP version 7
 * File EventManagerAwareInterface.php
 * 
 * @category Interface
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\EventManager;

/**
 * Interface EventManagerAwareInterface
 * 
 * @category Interface
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface EventManagerAwareInterface
{
    /**
     * Method setEventManager
     *
     * @param EventManagerInterface $EventManager EventManager
     * 
     * @return mixed
     */
    public function setEventManager(EventManagerInterface $EventManager);

    /**
     * Method getEventManager
     *
     * @return EventManagerInterface $EventManager
     */
    public function getEventManager();
}
