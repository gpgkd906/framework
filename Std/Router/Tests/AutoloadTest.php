<?php
/**
 * PHP version 7
 * File AutoloadTest.php
 *
 * @category UnitTest
 * @package  Std\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\Router\Tests;

use PHPUnit\Framework\TestCase;
use Std\Router;
use Framework\ObjectManager\ObjectManager;

/**
 * Class AutoloadTest
 *
 * @category UnitTest
 * @package  Std\Router
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
        $this->assertTrue(interface_exists(Router\RouterManagerInterface::class));
        $this->assertTrue(interface_exists(Router\RouterManagerAwareInterface::class));
        // class
        $this->assertTrue(class_exists(Router\AbstractRouter::class));
        $this->assertTrue(class_exists(Router\Http\Router::class));
        $this->assertTrue(class_exists(Router\Console\Router::class));
        $this->assertTrue(class_exists(Router\RouterManager::class));
        // trait
        $this->assertTrue(trait_exists(Router\RouterManagerAwareTrait::class));
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
        $ObjectManager = ObjectManager::getSingleton();
        $RouterManager = $ObjectManager->create(
            Router\RouterManagerInterface::class,
            Router\RouterManager::class
        );
        $this->assertInstanceOf(Router\RouterManagerInterface::class, $RouterManager);
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
