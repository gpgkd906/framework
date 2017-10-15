<?php
/**
 * PHP version 7
 * File ObjectManagerTest.php
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
use Framework\ObjectManager\ObjectManager;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\SingletonInterface;
use Framework\ObjectManager\FactoryInterface;
use Framework\ObjectManager\Tests\Stub;
use Framework\ObjectManager\Tests\StubClosure;
use Framework\ObjectManager\Tests\StubFactory;
use Framework\ObjectManager\Tests\StubSingleton;
use Framework\ObjectManager\Tests\StubExport;
use Framework\ObjectManager\Tests\StubInterface;
use Framework\ObjectManager\Tests\StubVirtualInterface;
use Closure;

/**
 * Class ObjectManagerTest
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ObjectManagerTest extends TestCase implements
    ObjectManagerAwareInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
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
        ObjectManager::getSingleton()->init();
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
     * Test testObjectManagerInit
     *
     * @return void
     */
    public function testObjectCreate()
    {
        $ObjectManager = $this->getObjectManager();
        $this->assertInstanceOf(ObjectManager::class, $ObjectManager);
        // 基本型
        $Stub = $ObjectManager->create(Stub\TestInterface::class, Stub\Test::class);
        $this->assertInstanceOf(Stub\TestInterface::class, $Stub);
        // Singleton型
        $StubSingleton = $ObjectManager->create(StubSingleton\TestInterface::class, StubSingleton\Test::class);
        $this->assertInstanceOf(StubSingleton\TestInterface::class, $StubSingleton);
        // Factory型
        $StubFactory = $ObjectManager->create(StubFactory\TestInterface::class, StubFactory\TestFactory::class);
        $this->assertInstanceOf(StubFactory\TestInterface::class, $StubFactory);
        // Closure型
        $StubClosure = $ObjectManager->create(StubClosure\TestInterface::class, function () {
            return new StubClosure\Test;
        });
        $this->assertInstanceOf(StubClosure\TestInterface::class, $StubClosure);
        // Export型
        $ObjectManager->export([
            StubExport\TestInterface::class => StubExport\Test::class
        ]);
        $StubExport = $ObjectManager->create(StubExport\TestInterface::class);
        $this->assertInstanceOf(StubExport\TestInterface::class, $StubExport);
        // Instace型
        $DateTime = $ObjectManager->create(\DateTime::class);
        $this->assertInstanceOf(\DateTime::class, $DateTime);
        // Void
        $Void = $ObjectManager->create(Void::class);
        $this->assertNull($Void);
    }

    /**
     * Test testObjectManagerInit
     *
     * @return void
     */
    public function testInjectDependency()
    {
        // InterfaceかClassとマッピングしている状態のDI
        $ObjectManager = $this->getObjectManager();
        $Test = $ObjectManager->get(Stub\Test::class, function () {
            return new class implements Stub\TestAwareInterface {
                use Stub\TestAwareTrait;
            };
        });
        $this->assertInstanceOf(Stub\Test::class, $Test->getTest());
        // DIにより、ObjectManagerの内部にも対象Objectを管理するようになる。
        $Test2 = $ObjectManager->get(Stub\TestInterface::class);
        $this->assertInstanceOf(Stub\TestInterface::class, $Test2);
        // InterfaceのみのDI
        $ObjectManager->export([
            StubInterface\TestInterface::class => function () {
                return new Class implements StubInterface\TestInterface {};
            }
        ]);
        $Test = $ObjectManager->create(stdClass::class, function () {
            return new class implements StubInterface\TestAwareInterface {
                use StubInterface\TestAwareTrait;
            };
        });
        $this->assertInstanceOf(StubInterface\TestInterface::class, $Test->getTest());
        $ObjectManager->export([
            StubInterface\Test::class => function () {
                return new Class implements StubInterface\TestInterface {};
            }
        ]);
        $Test = $ObjectManager->create(stdClass::class, function () {
            return new class implements StubInterface\TestAwareInterface {
                use StubInterface\TestAwareTrait;
            };
        });
        $this->assertInstanceOf(StubInterface\TestInterface::class, $Test->getTest());
        // 仮想インタフェース
        $ObjectManager->export([
            StubVirtualInterface\TestInterface::class => function () {
                return new Class implements Stub\TestInterface {};
            }
        ]);
        $Test = $ObjectManager->create(stdClass::class, function () {
            return new class implements StubVirtualInterface\TestAwareInterface {
                use StubVirtualInterface\TestAwareTrait;
            };
        });
        $this->assertInstanceOf(Stub\TestInterface::class, $Test->getTest());
    }
}
