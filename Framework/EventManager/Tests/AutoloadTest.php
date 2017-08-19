<?php
/**
 * PHP version 7
 * File AutoloadTest.php
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
use Framework\EventManager;
use Framework\EventManager\Tests\Stub\EventTarget;

/**
 * Class AutoloadTest
 * 
 * @category UnitTest
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class AutoloadTest extends TestCase
{
    /**
     * Method testAutoload
     *
     * @return  null
     * @example
     * @since
     */
    public function testAutoload()
    {
        // interface
        $this->assertTrue(interface_exists(EventManager\EventManagerInterface::class));
        $this->assertTrue(interface_exists(EventManager\EventManagerAwareInterface::class));
        $this->assertTrue(interface_exists(EventManager\EventTargetInterface::class));
        // class
        $this->assertTrue(class_exists(EventManager\Event::class));
        $this->assertTrue(class_exists(EventManager\EventManager::class));
        // trait
        $this->assertTrue(trait_exists(EventManager\EventManagerAwareTrait::class));
        $this->assertTrue(trait_exists(EventManager\EventTargetTrait::class));
    }

    /**
     * Method testInstance
     *
     * @return  null
     * @example
     * @since
     */
    public function testInstance()
    {
        // EventManager
        $eventManager = EventManager\EventManager::getSingleton();
        $this->assertInstanceOf(EventManager\EventManager::class, $eventManager);
        // Event 
        $event = $eventManager->createEvent('Test');
        $this->assertInstanceOf(EventManager\Event::class, $event);
        // EventTarget
        $target = $this->createMock(EventTarget::class);
        $this->assertInstanceOf(EventTarget::class, $target);
    }
}
