<?php
declare(strict_types=1);

namespace Framework\Router;

use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\Config\ConfigModel;
use Exception;

abstract class AbstractRouter implements RouterInterface, ObjectManagerAwareInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\ObjectManager\SingletonTrait;

    const ERROR_INVALID_JOINSTEP = "error: invalid join-step";
    const ERROR_OVER_MAX_DEPTHS = "error: over max_depths";
    const INDEX = 'index';

    static private $instances = [];

    private $config = null;
    private $request = null;
    private $routerList = [];

    abstract protected function loadRouter();

    public function __construct()
    {
        $this->config = ConfigModel::getConfigModel([
            "scope" => static::class,
            "property" => ConfigModel::READONLY
        ]);
        $this->index = self::INDEX;
    }


    public function dispatch()
    {
        if ($this->request === null) {
            $this->loadRouter();
            if (empty($this->request)) {
                $this->request = $this->parseRequest();
            }
            $controller = $this->request['controller'];
            if (isset($this->routerList[$controller])) {
                $this->request['controller'] = $this->routerList[$controller];
            }
        }
        return $this->request;
    }

    abstract public function getParam();

    abstract public function parseRequest();

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

    public function getindex()
    {
        return $this->index;
    }

    public function setConfig($config)
    {
        return $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function register($route)
    {
        foreach ($route as $req => $controller) {
            $this->routerList[$req] = $controller;
        }
    }

    public function getRouterList()
    {
        return $this->routerList;
    }
}
