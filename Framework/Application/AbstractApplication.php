<?php

namespace Framework\Application;

use Framework\Config\ConfigModel\ConfigModelInterface;
use Framework\Log\ErrorHandler;
use Framework\Event\Event\EventManager;
use Exception;

abstract class AbstractApplication implements ApplicationInterface
{
    const ERROR_NEED_GLOBAL_CONFIG = "error: need global config";
    const ERROR_INVALID_CONTROLLER_LABEL = "error: invalid_controller_label: %s";
    //
    const DEFAULT_PLUGINMANAGER = "Framework\Plugin\Plugin\PluginManager";
    const DEFAULT_MODEL_NAMESPACE = "Framework\Model";
    const DEFAULT_SERVICE_NAMESPACE = "Framework\Service";
    //
    const DEFAULT_TIMEZONE = "Asia/Tokyo";
    
    private $config = null;
    private $eventManager = null;
    private $pluginManager = null;

    /**
     *
     * @api
     * @var mixed $routeModel 
     * @access private
     * @link
     */
    private $routeModel = null;

    /**
     * 
     * @api
     * @param mixed $routeModel
     * @return mixed $routeModel
     * @link
     */
    public function setRouteModel ($routeModel)
    {
        return $this->routeModel = $routeModel;
    }

    /**
     * 
     * @api
     * @return mixed $routeModel
     * @link
     */
    public function getRouteModel ()
    {
        return $this->routeModel;
    }

    /**
     *
     * @api
     * @var mixed $serviceManager 
     * @access private
     * @link
     */
    private $serviceManager = null;

    /**
     * 
     * @api
     * @param mixed $serviceManager
     * @return mixed $serviceManager
     * @link
     */
    public function setServiceManager ($serviceManager)
    {
        return $this->serviceManager = $serviceManager;
    }

    /**
     * 
     * @api
     * @return mixed $serviceManager
     * @link
     */
    public function getServiceManager ()
    {
        if($this->serviceManager === null) {
            $this->serviceManager = new serviceManager;
            $this->serviceManager->setApplication($this);
        }
        return $this->serviceManager;
    }

    public function __construct(ConfigModelInterface $config)
    {
        $this->setConfig($config);
        $useErrorHandler = $this->config->getConfig("ErrorHandler", true);
        if($useErrorHandler) {
            ErrorHandler::setup();
        }
        $serviceManager = $this->getServiceManager();
        $this->setEventManager($serviceManager->get('Event', 'EventManager'));
        $pluginManager = $serviceManager->get('Plugin', 'PluginManager');        
        $pluginManager->initPlugins();
        $this->setPluginManager($pluginManager);
    }
     
    public function setConfig($config)
    {        
        $this->config = $config;
        if($this->config->getConfig('timezon')) {
            date_default_timezone_set($this->config->getConfig('timezon'));
        } else {
            date_default_timezone_set(static::DEFAULT_TIMEZONE);
        }
    }

    public function getConfig($key = null)
    {
        if($key === null) {
            return $this->config;
        } else {
            return $this->config->getConfig($key);
        }
    }
    
    abstract  public function run();
    
    abstract  public function getController();

    public function setPluginManager($pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    public function setEventManager($eventManager)
    {
        $this->eventManager = $eventManager;
    }
    
    public function getEventManager()
    {
        return $this->eventManager;
    }
}
