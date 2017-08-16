<?php
/**
 * PHP version 7
 * File Event.php
 * 
 * @category Event
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\EventManager;

/**
 * Class Event
 * 
 * @category Class
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class Event
{
    private $_name = null;
    private $_data = null;
    private $_target = null;
    private $_defaultPrevented = false;
    private $_bubbles = true;
    private $_cancelable = true;

    /**
     * Constructor
     *
     * @param string $name   EventName
     * @param array  $config EventConfig
     */
    public function __construct($name, $config = [])
    {
        $this->_name = $name;
        $this->_cancelable = $config['cancelable'] ?? true;
        $this->_bubbles = $config['bubbles'] ?? true;
    }

    /**
     * Method getName
     *
     * @return string $eventName
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Method setData
     *
     * @param mixed $data EventData
     * 
     * @return this
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * Method getData
     *
     * @return mixed $eventData
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Method setTarget
     *
     * @param EventTargetInterface $target EventTarget
     * 
     * @return this
     */
    public function setTarget($target)
    {
        $this->_target = $target;
        return $this;
    }

    /**
     * Method getTarget
     *
     * @return EventTargetInterface $eventTarget
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Method isDefaultPrevented
     *
     * @return boolean
     */
    public function isDefaultPrevented()
    {
        return $this->_defaultPrevented;
    }

    /**
     * Method isBubbles
     *
     * @return boolean
     */
    public function isBubbles()
    {
        return $this->_bubbles;
    }

    /**
     * Method preventDefault
     *
     * @return this
     */
    public function preventDefault()
    {
        if ($this->_cancelable) {
            $this->_defaultPrevented = true;
        }
        return $this;
    }

    /**
     * Method stopPropagation
     *
     * @return this
     */
    public function stopPropagation()
    {
        if ($this->_cancelable) {
            $this->_bubbles = false;
        }
        return $this;
    }

    /**
     * Method stopImmediatePropagation
     *
     * @return this
     */
    public function stopImmediatePropagation()
    {
        if ($this->_cancelable) {
            $this->_defaultPrevented = true;
            $this->_bubbles = false;
        }
        return $this;
    }

    /**
     * Method resetDefaultPrevent
     *
     * @return this
     */
    public function resetDefaultPrevent()
    {
        $this->_defaultPrevented = false;
        return $this;
    }
}
