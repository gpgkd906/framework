<?php
/**
 * PHP version 7
 * File EventManager.php
 *
 * @category EventManager
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\EventManager;

use Framework\ObjectManager\SingletonInterface;
use Framework\Service\CacheService\CacheServiceAwareInterface;

/**
 * Class EventManager
 *
 * @category Class
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class EventManager implements
    EventManagerInterface,
    CacheServiceAwareInterface,
    SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;
    use \Framework\Service\CacheService\CacheServiceAwareTrait;

    const ERROR_EVENT_STACK_EXISTS = "error: event [%s] is loop-triggered in class [%s]'s eventStack;";
    const ERROR_UNDEFINED_EVENT_TRIGGER = "error: undefiend event trigger [%s] in class [%s]";
    const ERROR_INVALID_CALLBACK_ADD_EVENT = "error: invalid callback with add event [%s]";
    const ERROR_INVALID_CALLBACK_REMOVE_EVENT = "error: invalid callback with remove event [%s]";

    private $_eventQueue = [];
    private $_triggerScope = [];
    private $_triggerPool = [];
    private $_propagationChainPool = [];
    private $_cache = null;
    /**
     * Contrustor
     */
    public function __construct()
    {

    }

    /**
     * getCache
     *
     * @return void
     */
    public function getCache()
    {
        if ($this->_cache === null) {
            $this->_cache = $this->getCacheService()->delegate(__CLASS__);
        }
        return $this->_cache;
    }

    /**
     * setCache
     *
     * @param [type] $cache
     * @return void
     */
    public function setCache($cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Method addEventListener
     *
     * @param string|EventTarget $class    classOrName
     * @param string|Event       $event    eventOrName
     * @param callable           $listener Listener
     *
     * @return this
     */
    public function addEventListener($class, $event, callable $listener)
    {
        $trigger = $this->getTrigger($class, $event);
        if (!isset($this->_eventQueue[$trigger])) {
            $this->_eventQueue[$trigger] = [];
        }
        $this->_eventQueue[$trigger][] = $listener;
        return $this;
    }

    /**
     * Method removeEventListener
     *
     * @param string|EventTarget $class    classOrName
     * @param string|Event       $event    eventOrName
     * @param callable           $listener Listener
     *
     * @return this
     */
    public function removeEventListener($class, $event, callable $listener)
    {
        $trigger = $this->getTrigger($class, $event);
        if (!isset($this->_eventQueue[$trigger])) {
            return false;
        }
        foreach ($this->_eventQueue[$trigger] as $key => $call) {
            if ($listener == $call) {
                unset($this->_eventQueue[$trigger][$key]);
                break;
            }
        }
    }

    /**
     * Method getEventListeners
     *
     * @param string|EventTarget $class classOrName
     * @param string|Event       $event eventOrName
     *
     * @return array Listeners
     */
    public function getEventListeners($class, $event)
    {
        if ($class instanceof EventTargetInterface) {
            return $class->getEventListeners($event);
        }
        $trigger = $this->getTrigger($class, $event);
        if (empty($trigger)) {
            return [];
        }
        if (!isset($this->_eventQueue[$trigger])) {
            $this->_eventQueue[$trigger] = [];
        }
        return $this->_eventQueue[$trigger];
    }

    /**
     * Method dispatchEvent
     *
     * @param string|EventTarget $class classOrName
     * @param Event              $Event eventOrName
     *
     * @return string triggerScope
     */
    public function dispatchEvent($class, Event $Event)
    {
        if (in_array($Event, $this->_triggerScope)) {
            throw new \Exception(sprintf(self::ERROR_EVENT_STACK_EXISTS, $Event->getName(), $class));
        }
        $this->_triggerScope[] = $Event;
        foreach (self::getPropagationChain($class) as $propagation) {
            if ($Event->isBubbles() === false && $propagation !== $class) {
                break;
            }
            foreach ($this->getEventListeners($propagation, $Event) as $key => $call) {
                if ($Event->isDefaultPrevented()) {
                    $Event->resetDefaultPrevent();
                    break;
                }
                call_user_func($call, $Event);
            }
        }
        return array_pop($this->_triggerScope);
    }

    /**
     * Method dispatchTargetEvent
     *
     * @param EventTargetInterface $target      EventTarget
     * @param string               $targetClass EventTargetClass
     * @param Event                $Event       Event
     *
     * @return string triggerScope
     */
    public function dispatchTargetEvent(EventTargetInterface $target, $targetClass, Event $Event)
    {
        if (in_array($Event, $this->_triggerScope)) {
            throw new \Exception(sprintf(self::ERROR_EVENT_STACK_EXISTS, $Event->getName(), $targetClass));
        }
        $this->_triggerScope[] = $Event;
        $trigger = $this->getTrigger($targetClass, $Event);
        if (!empty($trigger)) {
            foreach ($target->getEventListeners($Event->getName(), $trigger) as $key => $call) {
                if ($Event->isDefaultPrevented()) {
                    $Event->resetDefaultPrevent();
                    break;
                }
                call_user_func($call, $Event);
            }
        }
        array_pop($this->_triggerScope);
        return $this->dispatchEvent($targetClass, $Event);
    }

    /**
     * Method getCurrentEvent
     *
     * @return Event $event
     */
    public function getCurrentEvent()
    {
        $triggerScopeLength = count($this->_triggerScope) - 1;
        return $this->_triggerScope[$triggerScopeLength] ? $this->_triggerScope[$triggerScopeLength] : null;
    }

    /**
     * Method getTrigger
     *
     * @param string       $class ClassName
     * @param string|Event $event EventName
     *
     * @return string TriggerName
     */
    public function getTrigger($class, $event)
    {
        if ($event instanceof Event) {
            $event = $event->getName();
        }
        $triggerPool = $this->initTrigger($class);
        if (isset($triggerPool[$event])) {
            return $triggerPool[$event];
        }
    }

    /**
     * Method initTrigger
     *
     * @param string $class ClassName
     *
     * @return array eventTriggers
     */
    public function initTrigger($class)
    {
        if (!isset($this->_triggerPool[$class])) {
            $triggerPool = $this->getCache()->getItem('triggerPool');
            if (!$triggerPool) {
                $triggerPool = [];
            }
            if (isset($triggerPool[$class])) {
                $this->_triggerPool[$class] = $triggerPool[$class];
            } else {
                $reflection = new \ReflectionClass($class);
                $eventTrigger = [];
                foreach ($reflection->getConstants() as $constantName => $val) {
                    //TRIGGER_が始まるトリッガを拾う
                    if (strpos($constantName, 'TRIGGER_') === 0) {
                        //クラス情報をトリッガにセットする
                        $eventTrigger[$val] = $class . "::" . $val;
                    }
                }
                $this->_triggerPool[$class] = $eventTrigger;
                $this->getCache()->setItem('triggerPool', $this->_triggerPool);
            }
        }
        return $this->_triggerPool[$class];
    }

    /**
     * Method getPropagationChain
     *
     * @param string $class ClassName
     *
     * @return array propagationChains
     */
    public function getPropagationChain($class)
    {
        if (!isset($this->_propagationChainPool[$class])) {
            $this->_propagationChainPool[$class] = [$class] + class_parents($class) + class_implements($class);
        }
        return $this->_propagationChainPool[$class];
    }

    /**
     * Method createEvent
     *
     * @param string $name EventName
     *
     * @return Event $event
     */
    public function createEvent($name)
    {
        return new Event($name);
    }
}
