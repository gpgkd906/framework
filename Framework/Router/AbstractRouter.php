<?php
/**
 * PHP version 7
 * File AbstractRouter.php
 *
 * @category Interface
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Router;

use Zend\Diactoros\ServerRequestFactory;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\SingletonInterface;
use Framework\Service\CacheService\CacheServiceAwareInterface;
use Framework\EventManager\EventTargetInterface;
use Framework\Config\ConfigModel;
use Exception;

/**
 * Class AbstractRouter
 *
 * @category Class
 * @package  Framework\Router
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractRouter implements
    RouterInterface,
    ObjectManagerAwareInterface,
    EventTargetInterface,
    SingletonInterface,
    CacheServiceAwareInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\EventManager\EventTargetTrait;
    use \Framework\ObjectManager\SingletonTrait;
    use \Framework\Service\CacheService\CacheServiceAwareTrait;

    const ERROR_INVALID_JOINSTEP = "error: invalid join-step";
    const ERROR_OVER_MAX_DEPTHS = "error: over max_depths";
    const INDEX = 'index';

    const TRIGGER_ROUTERLIST_LOADED = 'router list loaded';
    const TRIGGER_ROUTEMISS = 'route miss';

    private $_request = null;
    private $_routerList = null;
    protected $request_param = null;

    /**
     * Abstract Method loadRouter
     *
     * @return void
     */
    abstract public function loadRouter();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->index = self::INDEX;
    }

    /**
     * Method dispatch
     *
     * @return array $request
     */
    public function dispatch()
    {
        if ($this->_request === null) {
            $routerList = $this->getRouterList();
            if (empty($this->_request)) {
                $this->_request = $this->parseRequest();
            }
            $controller = $this->_request['controller'];
            $this->_request['controller'] = null;
            if (isset($routerList[$controller])) {
                $this->_request['controller'] = $routerList[$controller];
            } else {
                $this->triggerEvent(static::TRIGGER_ROUTEMISS);
            }
        }
        return $this->_request;
    }

    /**
     * Method setRequest
     *
     * @param array $request requestData
     *
     * @return this
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Method getRequest
     *
     * @return array $request requestData
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Abstract Method getPara
     *
     * @return mixed
     */
    abstract public function getParam();

    /**
     * Method setParam
     *
     * @param array $param requestParam
     *
     * @return this
     */
    public function setParam($param)
    {
        $this->request_param = $param;
        return $this;
    }
    /**
     * Abstract Method parseRequest
     *
     * @return mixed
     */
    abstract public function parseRequest();

    /**
     * Method getIndex
     *
     * @return string $index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Method register
     *
     * @param array $route routeInfo
     *
     * @return this
     */
    public function register($route)
    {
        foreach ($route as $req => $controller) {
            $this->_routerList[$req] = $controller;
        }
        return $this;
    }

    /**
     * Method getRouterList
     *
     * @return array $routerList
     */
    public function getRouterList()
    {
        if (!$this->_routerList) {
            $cache = $this->getCacheService()->getCache('route');
            if ($routerList = $cache->getItem(static::class)) {
                $this->setRouterList($routerList);
            } else {
                $this->loadRouter();
                $cache->setItem(static::class, $this->_routerList);
            }
            $this->triggerEvent(static::TRIGGER_ROUTERLIST_LOADED);
        }
        return $this->_routerList;
    }

    /**
     * Method setRouterList
     *
     * @param array $routerList routerList
     *
     * @return this
     */
    public function setRouterList($routerList)
    {
        $this->_routerList = $routerList;
        return $this;
    }

    /**
     * Method getAction
     *
     * @return string $action
     */
    public function getAction()
    {
        $request = $this->getRequest();
        $action = $request["action"];
        return $action;
    }

    /**
     * Method getController
     *
     * @return string $controller
     */
    public function getController()
    {
        $request = $this->getRequest();
        return $request['controller'];
    }
}
