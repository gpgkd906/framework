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
use Framework\ObjectManager\Tests\StubClass;
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
     * Test testInjectDependency
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
        $this->assertInstanceOf(Stub\TestInterface::class, $Test->getTest());
        // DIにより、ObjectManagerの内部にも対象Objectを管理するようになる。
        $this->assertInstanceOf(Stub\TestInterface::class, $ObjectManager->get(Stub\TestInterface::class));
        // InterfaceのみのDI
        $ObjectManager->export([
            StubInterface\TestInterface::class => function () {
                return new Class implements StubInterface\TestInterface {};
            }
        ]);
        $Test = $ObjectManager->create(null, function () {
            return new class implements StubInterface\TestAwareInterface {
                use StubInterface\TestAwareTrait;
            };
        });
        $this->assertInstanceOf(StubInterface\TestInterface::class, $Test->getTest());
        $Test2 = $ObjectManager->create(null, function () {
            return new class implements StubInterface\TestAwareInterface {
                use StubInterface\TestAwareTrait;
            };
        });
        // 別々のObjectが同じInterfaceをInjectできる。
        $this->assertNotEquals($Test, $Test2);
        $this->assertInstanceOf(StubInterface\TestInterface::class, $Test2->getTest());
    }

    /**
     * Test testInjectClassDependency
     *
     * @runInSeparateProcess
     * @return void
     */
    public function testInjectClassDependency()
    {
        // classのみによる、DIのテスト
        $ObjectManager = $this->getObjectManager();
        // classによる宣言を行う
        $ObjectManager->export([
            StubClass\Test::class => function () {
                return new Class extends StubClass\Test {};
            }
        ]);
        $Test = $ObjectManager->create(null, function () {
            return new class implements StubClass\TestAwareInterface {
                use StubClass\TestAwareTrait;
            };
        });
        $this->assertInstanceOf(StubClass\Test::class, $Test->getTest());
        $Test2 = $ObjectManager->create(null, function () {
            return new class implements StubClass\TestAwareInterface {
                use StubClass\TestAwareTrait;
            };
        });
        // 別々のObjectが同じInterfaceをInjectできる。
        $this->assertNotEquals($Test, $Test2);                
        $this->assertInstanceOf(StubClass\Test::class, $Test2->getTest());
    }

    /**
     * Test testInjectVirtualDependency
     *
     * @runInSeparateProcess
     * @return void
     */
    public function testInjectVirtualDependency()
    {
        $ObjectManager = $this->getObjectManager();
        // 仮想class
        $ObjectManager->export([
            StubVirtualInterface\Test::class => function () {
                return new Class implements Stub\TestInterface {};
            }
        ]);
        $Test = $ObjectManager->create(null, function () {
            return new class implements StubVirtualInterface\TestAwareInterface {
                use StubVirtualInterface\TestAwareTrait;
            };
        });
        $this->assertInstanceOf(Stub\TestInterface::class, $Test->getTest());        
        // 仮想Interface
        $ObjectManager->export([
            StubVirtualInterface\TestInterface::class => function () {
                return new Class implements Stub\TestInterface {};
            }
        ]);
        $Test2 = $ObjectManager->create(null, function () {
            return new class implements StubVirtualInterface\TestAwareInterface {
                use StubVirtualInterface\TestAwareTrait;
            };
        });
        $this->assertNotEquals($Test, $Test2);                
        $this->assertInstanceOf(Stub\TestInterface::class, $Test2->getTest());
    }
}
