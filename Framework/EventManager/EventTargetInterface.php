<?php
/**
 * PHP version 7
 * File EventTargetInterface.php
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
 * Interface EventTargetInterface
 * 
 * @category Interface
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface EventTargetInterface
{
    const TRIGGER_INIT = "Initiation";
    const TRIGGER_INITED = "Initialized";
    const TRIGGER_DEINIT = "Deinitiation";

    /**
     * Method addEventListener
     *
     * @param string   $eventName EventName
     * @param callable $listener  Listener
     *  
     * @return mixed
     */
    public function addEventListener($eventName, callable $listener);

    /**
     * Method removeEventListener
     *
     * @param string   $eventName EventName
     * @param callable $listener  Listener
     *  
     * @return mixed
     */
    public function removeEventListener($eventName, callable $listener);

    /**
     * Method dispatchEvent
     *
     * @param Event $event Event
     * 
     * @return mixed
     */
    public function dispatchEvent(Event $event);
}
