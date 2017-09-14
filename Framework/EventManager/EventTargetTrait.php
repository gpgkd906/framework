<?php
/**
 * PHP version 7
 * File EventTargetTrait.php
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
 * Trait EventTargetTrait
 *
 * @category Trait
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait EventTargetTrait
{
    use EventManagerAwareTrait;
    private $_eventListeners = [];

    /**
     * Method addEventListener
     *
     * @param string   $event    EventName
     * @param callable $listener Listener
     *
     * @return void
     */
    public function addEventListener($event, callable $listener)
    {
        $trigger = $this->_getTrigger($event);
        if (!isset($this->_eventListeners[$trigger])) {
            $this->_eventListeners[$trigger] = [];
        }
        $this->_eventListeners[$trigger][] = $listener;
    }

    /**
     * Method removeEventListener
     *
     * @param string   $event    EventName
     * @param callable $listener Listener
     *
     * @return void
     */
    public function removeEventListener($event, callable $listener)
    {
        $trigger = $this->_getTrigger($event);
        if (!isset($this->_eventListeners[$trigger])) {
            $this->_eventListeners[$trigger] = [];
        }
        foreach ($this->_eventListeners[$trigger] as $key => $call) {
            if ($listener == $call) {
                unset($this->_eventListeners[$trigger][$key]);
                break;
            }
        }
    }

    /**
     * Method getEventListeners
     *
     * @param string|Event $event   EventOrName
     * @param string|null  $trigger triggerName
     *
     * @return array Listeners
     */
    public function getEventListeners($event, $trigger = null): array
    {
        $trigger = $trigger ? $trigger : $this->_getTrigger($event);
        if (empty($trigger)) {
            return [];
        }
        if (!isset($this->_eventListeners[$trigger])) {
            $this->_eventListeners[$trigger] = [];
        }
        return $this->_eventListeners[$trigger];
    }

    /**
     * Method dispatchEvent
     *
     * @param Event $Event Event
     *
     * @return void
     */
    public function dispatchEvent(Event $Event)
    {
        $Event->setTarget($this);
        $this->getEventManager()->dispatchTargetEvent($this, static::class, $Event);
    }

    /**
     * Method triggerEvent
     *
     * @param string $event      EventName
     * @param array  $parameters EventData
     *
     * @return this
     */
    public function triggerEvent($event, $parameters = [])
    {
        $Event = $this->getEventManager()->createEvent($event);
        $Event->setData($parameters);
        $this->dispatchEvent($Event);
        return $this;
    }

    /**
     * Method getCurrentEvent
     *
     * @return Event $event;
     */
    public function getCurrentEvent(): Event
    {
        return $this->getEventManager()->getCurrentEvent();
    }

    /**
     * Method _getTrigger
     *
     * @param string|Event $event EventOrName
     *
     * @return string
     */
    private function _getTrigger($event): ?string
    {
        if ($trigger = $this->getEventManager()->getTrigger(static::class, $event)) {
            return $trigger;
        }
        return null;
    }
}
