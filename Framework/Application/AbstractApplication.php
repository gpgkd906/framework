<?php

namespace Framework\Application;

use Framework\Config\ConfigModel\ConfigModelInterface;
use Framework\Log\ErrorHandler;
use Framework\Plugin\PluginManager\PluginManager;
use Framework\ObjectManager\ObjectManager;
use Framework\EventManager;
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
     * summary
     * content
     * @api
     * @var mixed $routeModel
     * @access private
     * @link
     */
    private $routeModel = null;

    /**
     *
     * @api
     * @var mixed $objectManager
     * @access private
     * @link
     */
    private $objectManager = null;

    /**
     *
     * @api
     * @param mixed $objectManager
     * @return mixed $objectManager
     * @link
     */
    public function setObjectManager($objectManager)
    {
        return $this->objectManager = $objectManager;
    }

    /**
     *
     * @api
     * @return mixed $objectManager
     * @link
     */
    public function getObjectManager()
    {
        if ($this->objectManager === null) {
            $this->objectManager = ObjectManager::getSingleton();
            $this->objectManager->set(ApplicationInterface::class, $this);
        }
        return $this->objectManager;
    }

    public function __construct(ConfigModelInterface $config)
    {
        $this->setConfig($config);
        $useErrorHandler = $this->config->get("ErrorHandler", true);
        if ($useErrorHandler) {
            ErrorHandler::setup();
        }
        $objectManager = $this->getObjectManager();
        $objectManager->create(EventManager\EventManager::class);
        $objectManager->init();
        // $pluginManager = $objectManager->get(PluginManager::class);
        // $pluginManager->initPlugins();
    }

    public function setConfig($config)
    {
        $this->config = $config;
        if ($this->config->get('timezon')) {
            date_default_timezone_set($this->config->get('timezon'));
        } else {
            date_default_timezone_set(static::DEFAULT_TIMEZONE);
        }
    }

    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        } else {
            return $this->config->get($key);
        }
    }

    abstract  public function run();

    abstract  public function getController();
}
