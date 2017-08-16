<?php
/**
 * PHP version 7
 * File EventManagerInterface.php
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
 * Interface EventManagerInterface
 * 
 * @category Interface
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface EventManagerInterface
{
    /**
     * Method addEventListener
     *
     * @param string|EventTarget $class    classOrName
     * @param string|Event       $event    eventOrName
     * @param callable           $listener Listener
     * 
     * @return this
     */
    public function addEventListener($class, $event, callable $listener);

    /**
     * Method removeEventListener
     *
     * @param string|EventTarget $class    classOrName
     * @param string|Event       $event    eventOrName
     * @param callable           $listener Listener
     * 
     * @return this
     */
    public function removeEventListener($class, $event, callable $listener);

    /**
     * Method getEventListeners
     *
     * @param string|EventTarget $class classOrName
     * @param string|Event       $event eventOrName
     * 
     * @return array Listeners
     */
    public function getEventListeners($class, $event);

    /**
     * Method dispatchEvent
     *
     * @param string|EventTarget $class classOrName
     * @param Event              $Event eventOrName
     * 
     * @return mixed
     */
    public function dispatchEvent($class, Event $Event);

    /**
     * Method dispatchTargetEvent
     *
     * @param EventTargetInterface $target      EventTarget
     * @param string               $targetClass EventTargetClass
     * @param Event                $Event       Event
     * 
     * @return mixed
     */
    public function dispatchTargetEvent(EventTargetInterface $target, $targetClass, Event $Event);

    /**
     * Method createEvent
     *
     * @param string $name Name
     * 
     * @return Event
     */
    public function createEvent($name);
}
