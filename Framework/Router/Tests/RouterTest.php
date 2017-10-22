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
namespace Framework\Router\Tests;

use PHPUnit\Framework\TestCase;
use Framework\Router;
use Framework\Router\Http\Router as HttpRouter;
use Framework\Router\Console\Router as ConsoleRouter;
use Framework\ObjectManager\ObjectManager;

/**
 * Class RouterTest
 *
 * @category UnitTest
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class RouterTest extends TestCase
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
     * Test testHttpRouter
     *
     * @return void
     */
    public function testHttpRouter()
    {
        $routeList = [
            'test' => MockController::class,
            'test/index' => MockController::class
        ];
        // 第一个路由
        $Router = new HttpRouter;
        $Router->setRouterList($routeList);
        $Router->setRequestUri('test/index');
        $request = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        $this->assertEquals($request['controller'], MockController::class);
        // 第二个路由，实际场景下，每次页面访问只需要路由一次URL，因此路由结果会缓存在路由器内部
        // 我们需要测试不同的URL，就需要生成不同的路由器。
        $Router = new HttpRouter;
        $Router->setRouterList($routeList);
        $Router->setRequestUri('test');
        $request = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        $this->assertEquals($request['controller'], MockController::class);
        // 在标准路由场景中，除了全站维护时的维护界面之外，更多的情况是，不同的模块分别注册各自的路由。
        $Router = new HttpRouter;
        $Router->register(
            [
                'test' => MockController::class
            ]
        );
        $Router->register(
            [
                'test/index' => MockController::class
            ]
        );
        $this->assertEquals($routeList, $Router->getRouterList());
        $Router->setRequestUri('test');
        $request = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        $this->assertEquals($request['controller'], MockController::class);
        // 默认路由为index
        $routeList = [
            'index' => MockController::class,
        ];
        $Router = new HttpRouter;
        $Router->setRouterList($routeList);
        // 当requestUri为空文字时，路由器会发送至默认路由
        $Router->setRequestUri('');
        $request = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        $this->assertEquals($request['controller'], MockController::class);
        // 此外，当favicon不存在的时候，服务器也会把请求发送到Router里面，Router需要在最小的处理时间下返回分发
        $Router = new HttpRouter;
        $Router->setRequestUri('/favicon.ico');
        $this->assertTrue($Router->isFaviconRequest());
    }

    /**
     * @runInSeparateProcess
     * Test testHttpRouterWithMethod
     * 这里测试实际应用中，各个模块注册路由的处理
     * 框架本身各模块依赖于ObjectManager, 因此我们需要把Router注册到ObjectManager上
     *
     * @return void
     */
    public function testHttpRouterWithMethod()
    {
        // Router会根据请求的method来选择请求数据的来源，默认method为GET
        $Router = new HttpRouter;
        $_SERVER["REQUEST_METHOD"] = 'POST';
        $_GET = [
            'invalid'
        ];
        $_POST = [
            1, 2, 3
        ];
        $this->assertEquals($Router->getParam(), array_merge($_GET, $_POST));
    }


    /**
     * @runInSeparateProcess
     */
    public function testHttpRouterRedirect()
    {
        $routeList = [
            'index' => MockController::class,
        ];
        $Router = new HttpRouter;
        $Router->setRouterList($routeList);
        $requestUri = 'index/1';
        $Router->setRequestUri($requestUri);
        $Router->dispatch();
        // 我们也可反过来利用路由找到控制器所对应的超链接※考虑到超链接的特性，通过路由找到的超链接会在最前方加入 [/]
        $uri = $Router->linkto(MockController::class);
        $this->assertEquals($uri, '/index');
        // 注意，在UnitTest里进行跳转会产生Error
        $Router->reload();
        $this->assertContains(
            "Location: /$requestUri", xdebug_get_headers()
        );
    }

    public function testHttpRouterRedirectException()
    {
        $routeList = [
            'index' => MockController::class,
        ];
        $Router = new HttpRouter;
        $Router->setRouterList($routeList);
        $requestUri = 'index/1';
        $Router->setRequestUri($requestUri);
        $Router->dispatch();
        // 当找不到对应的控制器时，会返回null
        $this->assertNull($Router->linkto(InValidMockController::class));
    }

    /**
     * Test testHttpRouterForApplication
     * 这里测试实际应用中，各个模块注册路由的处理
     * 框架本身各模块依赖于ObjectManager, 因此我们需要把Router注册到ObjectManager上
     *
     * @return void
     */
    public function testHttpRouterForApplication()
    {
        $ObjectManager = ObjectManager::getSingleton();
        $RouterManager = new Router\RouterManager;
        $ObjectManager->set(Router\RouterManagerInterface::class, $RouterManager);
        $Router = new HttpRouter;
        $RouterManager->register(Router::class, $Router);
        // 当空路由被请求分发URL的时候，空路由的路由器会自动搜索各模块的路由，并进行注册
        // 在实际的应用中，Router应当从$_SERVER['REQUEST_URI']访问
        $_SERVER['REQUEST_URI'] = '//';
        // $Router->setRequestUri('//');
        $request = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        // 实际的应用中，已经分发过的请求，会被缓存，可以直接取得
        $request2 = $Router->getRequest();
        $this->assertEquals($request, $request2);
        // URL中包含?的时候，我们需要把?进行特别处理
        $Router = new HttpRouter;
        ObjectManager::getSingleton()->set(Router\RouterInterface::class, $Router);
        $Router->setRequestUri('?test=2');
        $request2 = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        $this->assertEquals($request, $request2);
        // 实际的应用中，已经分发过的请求，会被缓存，可以直接取得
        $request3 = $Router->getRequest();
        $this->assertEquals($request3, $request2);
        // 另一方面，我们可以在分发请求之前设置分发内容，来实现一些全局功能
        // 例如全站维护之类
        $Router = new HttpRouter;
        ObjectManager::getSingleton()->set(Router\RouterInterface::class, $Router);
        $param = [
            'controller' => MockController::class,
            'action' => 'index',
            'param' => []
        ];
        $Router->setRequest($param);
        $request = $Router->getRequest();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        $this->assertEquals($param, $request);
        // 此外，我们也可以通过路由器直接取得各个项目的内容
        $this->assertContains($Router->getMethod(), ['post', 'get', 'put', 'delete', 'option']);
        $this->assertEquals($Router->getController(), MockController::class);
        $this->assertEquals($Router->getAction(), 'index');
        $this->assertEquals($Router->getParam(), []);
    }

    /**
     * Test testHttpRouterFailture
     * キャッシュキーが重複登録するとエラーになるため、別プロセスでテストを実行。
     *
     * @return void
     */
    public function testHttpRouterFailture()
    {
        $Router = new HttpRouter;
        // ObjectManager::getSingleton()->set(Router\RouterInterface::class, $Router);
        $Router->setRequestUri('index.php');
        $request = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
        $this->assertEmpty($request['controller']);
        $this->assertEmpty($request['action']);
    }
    /**
     * Test testConsoleRouterForApplication
     * 这里测试实际应用中，各个模块注册路由的处理
     * 框架本身各模块依赖于ObjectManager, 因此我们需要把Router注册到ObjectManager上
     *
     * @return void
     */
    public function testConsoleRouterForApplication()
    {
        // 接下来测试Console路由
        $ObjectManager = ObjectManager::getSingleton();
        $RouterManager = new Router\RouterManager;
        $ObjectManager->set(Router\RouterManagerInterface::class, $RouterManager);
        $Router = new ConsoleRouter;
        $RouterManager->register(Router::class, $Router);
        // Console路由访问Console参数
        global $argv;
        $argv = ['action', 'param=123'];
        $request = $Router->dispatch();
        $this->assertArrayHasKey('controller', $request);
        $this->assertArrayHasKey('action', $request);
    }

    /**
     * Test testConsoleRouterException
     *
     * @return void
     */
    public function testConsoleRouterException()
    {
        $this->expectException(\Exception::class);
        // 接下来测试Console路由
        $Router = new ConsoleRouter;
        $routeList = [
            'test' => ConsoleMockController::class
        ];
        $Router->setRouterList($routeList);
        $Router->setParam([]);
        $Router->dispatch();
        $Router->redirect(ConsoleMockController::class);
    }
}
