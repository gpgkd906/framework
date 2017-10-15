<?php
/**
 * PHP version 7
 * File AutoloadTest.php
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\ObjectManager\Tests;

use PHPUnit\Framework\TestCase;
use Framework\ObjectManager;
use Framework\ObjectManager\Tests\Stub\Test;
use Framework\ObjectManager\Tests\Stub\TestInterface;
use Framework\ObjectManager\Tests\Stub\TestAwareInterface;
use Framework\ObjectManager\Tests\Stub\TestAwareTrait;

/**
 * Class AutoloadTest
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
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
        $this->assertTrue(interface_exists(ObjectManager\ObjectManagerInterface::class));
        $this->assertTrue(interface_exists(ObjectManager\ObjectManagerAwareInterface::class));
        $this->assertTrue(interface_exists(ObjectManager\SingletonInterface::class));
        $this->assertTrue(interface_exists(ObjectManager\FactoryInterface::class));
        // class
        $this->assertTrue(class_exists(ObjectManager\ObjectManager::class));
        // trait
        $this->assertTrue(trait_exists(ObjectManager\ObjectManagerAwareTrait::class));
        $this->assertTrue(trait_exists(ObjectManager\SingletonTrait::class));
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
        // ObjectManager
        $ObjectManager = ObjectManager\ObjectManager::getSingleton();
        $this->assertInstanceOf(ObjectManager\ObjectManager::class, $ObjectManager);
    }
}
