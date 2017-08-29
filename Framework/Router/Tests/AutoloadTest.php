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
namespace Framework\Router\Tests;
 
use PHPUnit\Framework\TestCase;
use Framework\Router;
use Framework\ObjectManager\ObjectManager;

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
        $this->assertTrue(interface_exists(Router\RouterInterface::class));
        $this->assertTrue(interface_exists(Router\RouterAwareInterface::class));
        // class
        $this->assertTrue(class_exists(Router\AbstractRouter::class));
        $this->assertTrue(class_exists(Router\Http\Router::class));
        $this->assertTrue(class_exists(Router\Console\Router::class));
        // trait
        $this->assertTrue(trait_exists(Router\RouterAwareTrait::class));
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
        $ObjectManager = ObjectManager::getSingleton();
        $HttpRouter = $ObjectManager->create(
            Router\RouterInterface::class,
            Router\Http\Router::class
        );
        $this->assertInstanceOf(Router\RouterInterface::class, $HttpRouter);
        $ConsoleRouter = $ObjectManager->create(
            Router\RouterInterface::class,
            Router\Console\Router::class
        );
        $this->assertInstanceOf(Router\RouterInterface::class, $ConsoleRouter);
    }
}
