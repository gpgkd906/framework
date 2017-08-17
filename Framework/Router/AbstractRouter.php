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

use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\ObjectManager\SingletonInterface;
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
    SingletonInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\ObjectManager\SingletonTrait;

    const ERROR_INVALID_JOINSTEP = "error: invalid join-step";
    const ERROR_OVER_MAX_DEPTHS = "error: over max_depths";
    const INDEX = 'index';

    private $_config = null;
    private $_request = null;
    private $_routerList = [];

    /**
     * Abstract Method loadRouter
     *
     * @return void
     */
    abstract protected function loadRouter();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_config = ConfigModel::getConfigModel(
            [
                "scope" => static::class,
                "property" => ConfigModel::READONLY
            ]
        );
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
            $this->loadRouter();
            if (empty($this->_request)) {
                $this->_request = $this->parseRequest();
            }
            $controller = $this->_request['controller'];
            if (isset($this->_routerList[$controller])) {
                $this->_request['controller'] = $this->_routerList[$controller];
            }
        }
        return $this->_request;
    }

    /**
     * Abstract Method getPara
     *
     * @return mixed
     */
    abstract public function getParam();

    /**
     * Abstract Method parseRequest
     *
     * @return mixed
     */
    abstract public function parseRequest();

    /**
     * Method getReq
     *
     * @return string $req
     */
    public function getReq()
    {
        $param = $this->getParam();
        if (isset($param["req"])) {
            return $param["req"];
        } else {
            if (isset($_GET['req'])) {
                return $_GET['req'];
            }
            return $this->index;
        }
    }

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
     * Method setConfig
     *
     * @param ConfirModel $config ConfigModel
     * @return this
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Method getConfig
     *
     * @return ConfigModel $config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Method register
     *
     * @param array $route
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
        return $this->_routerList;
    }
}
