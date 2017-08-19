<?php
/**
 * PHP version 7
 * File EventManagerTest.php
 *
 * @category UnitTest
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\EventManager\Tests;
 
use PHPUnit\Framework\TestCase;
use Framework\EventManager\Tests\Stub\EventTarget;
use Framework\EventManager\Event;
use Framework\EventManager\EventManager;
use Framework\EventManager\EventTargetInterface;

/**
 * Class EventManagerTest
 *
 * @category UnitTest
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class EventManagerTest extends TestCase
{
    /**
    * setUpBeforeClass
    *
    * @api
    * @access
    * @return  null
    * @example
    * @since
    */
    public static function setUpBeforeClass()
    {
    }

    /**
    * tearDownAfterClass
    *
    * @api
    * @access
    * @return  null
    * @example
    * @since
    */
    public static function tearDownAfterClass()
    {
    }

    /**
     * Test testEventTarget
     *
     * @return void
     */
    public function testEventTarget()
    {
        $target = new EventTarget;
        $count = 0;
        $eventListenr = function ($event) use (&$count) {
            $count++;
            $this->assertEquals(EventTarget::TRIGGER_TEST, $event->getName());
            $this->assertEquals($event, $event->getTarget()->getCurrentEvent());
        };
        $target->addEventListener(
            EventTarget::TRIGGER_TEST,
            $eventListenr
        );
        $target->triggerEvent(EventTarget::TRIGGER_TEST);
        $this->assertEquals(1, $count);
        $target->triggerEvent(EventTarget::TRIGGER_TEST);
        $target->triggerEvent(EventTarget::TRIGGER_TEST);
        $this->assertEquals(3, $count);
        // 事件监听是可以删除的
        $target->removeEventListener(
            EventTarget::TRIGGER_TEST,
            $eventListenr
        );
        $target->triggerEvent(EventTarget::TRIGGER_TEST);
        $target->triggerEvent(EventTarget::TRIGGER_TEST);
        $this->assertEquals(3, $count);
        $target->removeEventListener(
            EventTarget::TRIGGER_INIT,
            $eventListenr
        );
        $this->assertEquals(3, $count);
        // test setData/getData
        $data = [1, "2", false];
        $target->addEventListener(
            EventTarget::TRIGGER_TEST,
            function ($event) use ($data) {
                $this->assertEquals($data, $event->getData());
            }
        );
        $target->triggerEvent(EventTarget::TRIGGER_TEST, $data);
    }

    /**
     * Test testEventTargetFailture
     *
     * @return void
     */
    public function testEventTargetFailture()
    {
        $target = new EventTarget;
        $count = 0;
        $eventListenr = function ($event) use (&$count) {
            $count++;
            $this->assertEquals(EventTarget::TRIGGER_TEST, $event->getName());
            $this->assertEquals($event, $event->getTarget()->getCurrentEvent());
        };
        // 当监听未定义的Event，不会产生任何动作，也不会产生错误信息。
        $event = 'Invalid Event';
        $target->addEventListener(
            $event,
            $eventListenr
        );
        $target->triggerEvent($event);
        $this->assertEquals(0, $count);
    }

    /**
     * Test testEventTargetException
     *
     * @return void
     */
    public function testEventTargetException()
    {
        $this->expectException(\Exception::class);
        $target = new EventTarget;
        // 在Event的内部，不允许激发同类型的Event，将会产生Exception
        $target->addEventListener(
            EventTarget::TRIGGER_INITED,
            function ($event) {
                $event->getTarget()->triggerEvent(EventTarget::TRIGGER_TEST);
            }
        );
        $target->addEventListener(
            EventTarget::TRIGGER_TEST,
            function ($event) {
                $event->getTarget()->triggerEvent(EventTarget::TRIGGER_INITED);
            }
        );
        $target->triggerEvent(EventTarget::TRIGGER_TEST);
    }

    /**
     * Test testEventManager
     *
     * @return void
     */
    public function testEventManager()
    {
        $eventManager = new EventManager;
        $event = $eventManager->createEvent(EventTarget::TRIGGER_INITED);
        $count = 0;
        $eventListenr = function ($event) use (&$count) {
            $count++;
            $this->assertEquals(EventTarget::TRIGGER_INITED, $event->getName());
        };
        // EventTargetInterface里定义了 TRIGGER_INITED, 允许进行监听
        $eventManager->addEventListener(
            EventTargetInterface::class,
            EventTarget::TRIGGER_INITED,
            $eventListenr
        );
        $eventManager->dispatchEvent(EventTarget::class, $event);
        $this->assertEquals(1, $count);
        // eventManage上的事件监听，也是可以删除的
        $eventManager->removeEventListener(
            EventTargetInterface::class,
            EventTarget::TRIGGER_INITED,
            $eventListenr
        );
        $eventManager->dispatchEvent(EventTarget::class, $event);
        $this->assertEquals(1, $count);
        // EventTargetInterface里并未定义TRIGGER_TEST, 无法进行监听
        $event = $eventManager->createEvent(EventTarget::TRIGGER_TEST);
        // addEventListener不会出错，但也不会产生任何效果
        $eventManager->addEventListener(
            EventTargetInterface::class,
            EventTarget::TRIGGER_TEST,
            $eventListenr
        );
        $eventManager->dispatchEvent(EventTarget::class, $event);
        $this->assertEquals(1, $count);
        // 但是 EventTarget的Class定义里有TRIGGER_TEST, 因此可以对EventTarget进行监听
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_TEST,
            function ($event) use (&$count) {
                $count++;
                $this->assertEquals(EventTarget::TRIGGER_TEST, $event->getName());
            }
        );
        $eventManager->dispatchEvent(EventTarget::class, $event);
        $this->assertEquals(2, $count);
    }

    /**
     * Test testEventManagerFailture
     *
     * @return void
     */
    public function testEventManagerFailture()
    {
        $eventManager = new EventManager;
        $count = 0;
        $eventListenr = function ($event) use (&$count) {
            $count++;
            $this->assertEquals(EventTarget::TRIGGER_TEST, $event->getName());
            $this->assertEquals($event, $event->getTarget()->getCurrentEvent());
        };
        // 当监听未定义的Event，不会产生任何动作，也不会产生错误信息。
        $event = 'Invalid Event';
        $eventManager->addEventListener(
            EventTarget::class,
            $event,
            $eventListenr
        );
        $eventManager->dispatchEvent(EventTarget::class, $eventManager->createEvent($event));
        $this->assertEquals(0, $count);
    }

    /**
     * Test testEventManagerException
     *
     * @return void
     */
    public function testEventManagerException()
    {
        $this->expectException(\Exception::class);
        $eventManager = new EventManager;
        // 在Event的内部，不允许激发同类型的Event，将会产生Exception
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_INITED,
            function ($event) use ($eventManager) {
                $eventManager->dispatchEvent(EventTarget::class, $eventManager->createEvent(EventTarget::TRIGGER_TEST));
            }
        );
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_TEST,
            function ($event) use ($eventManager) {
                $eventManager->dispatchEvent(EventTarget::class, $eventManager->createEvent(EventTarget::TRIGGER_INITED));
            }
        );
        $eventManager->dispatchEvent(EventTarget::class, $eventManager->createEvent(EventTarget::TRIGGER_INITED));
    }

    /**
     * Test testEventManager
     * 在testEventManager里，我们已经测试了类事件冒泡至接口
     * 接下来我们测试事件的非冒泡等处理
     *
     * @return void
     */
    public function testConfigurableEvent()
    {
        $event = new Event(
            EventTarget::TRIGGER_INITED,
            [
                'bubbles' => false
            ]
        );
        $this->assertFalse($event->isBubbles());
        $target = new EventTarget;
        $eventManager = new EventManager;
        $target->setEventManager($eventManager);
        $count = 0;
        $eventListenr = function ($event) use (&$count) {
            $count++;
        };
        $eventManager->addEventListener(
            EventTargetInterface::class,
            EventTarget::TRIGGER_INITED,
            $eventListenr
        );
        // Event设置为非冒泡, 因此类事件并不会会冒泡至接口定义
        $target->dispatchEvent($event);
        $this->assertEquals(0, $count);
        // 但对于事件本身的监听，则不应当受到影响
        $event = new Event(
            EventTarget::TRIGGER_TEST,
            [
                'bubbles' => false
            ]
        );
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_TEST,
            $eventListenr
        );
        $target->dispatchEvent($event);
        $this->assertEquals(1, $count);
        // 或者在冒泡途中，取消冒泡
        $count = 0;
        $eventManager = new EventManager;
        $target->setEventManager($eventManager);
        $eventManager->addEventListener(
            EventTargetInterface::class,
            EventTarget::TRIGGER_INITED,
            $eventListenr
        );
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_INITED,
            function ($event) {
                $event->stopPropagation();
            }
        );
        $target->triggerEvent(EventTarget::TRIGGER_INITED);
        $this->assertEquals(0, $count);
        // 也可以追加复数的事件监听，但于某一个监听中，中止同层级的事件监听，来实现一些比较复杂的处理
        $count = 0;
        $eventManager = new EventManager;
        $target->setEventManager($eventManager);
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_INITED,
            function ($event) {
                $event->preventDefault();
            }
        );
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_INITED,
            $eventListenr
        );
        $target->triggerEvent(EventTarget::TRIGGER_INITED);
        $this->assertEquals(0, $count);
        // 也可以同时取消同层级事件监听以及事件冒泡
        $count = 0;
        $eventManager = new EventManager;
        $target->setEventManager($eventManager);
        $eventManager->addEventListener(
            EventTargetInterface::class,
            EventTarget::TRIGGER_INITED,
            $eventListenr
        );
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_INITED,
            function ($event) {
                $event->stopImmediatePropagation();
            }
        );
        $eventManager->addEventListener(
            EventTarget::class,
            EventTarget::TRIGGER_INITED,
            $eventListenr
        );
        $target->triggerEvent(EventTarget::TRIGGER_INITED);
        $this->assertEquals(0, $count);
    }
}
