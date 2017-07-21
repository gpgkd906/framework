<?php

namespace Framework\ViewModel;

use Framework\EventManager\EventTargetInterface;
use Exception;

class ViewModelManager implements ViewModelManagerInterface
{
    const ERROR_INVALID_VIEWMODEL_CONFIG = "error: invalid viewmodel config";
    const ERROR_INVALID_VIEWMODEL = "error: invalid viewmodelname: %s";
    const ERROR_INVALID_TEMPLATE_VIEWMODEL = "error: invalid template viewModel";
    const ERROR_VIEWMODEL_DEFINED_ID = "error: viewId [%s] was defined before, change some new ID";

    private static $viewModelPool = [];
    //
    private static $namespace = null;
    private static $templateDir = null;

    /**
     *
     * @api
     * @var mixed $basePath
     * @access private
     * @link
     */
    private static $basePath = null;

    /**
     *
     * @api
     * @var mixed $objectManager
     * @access private
     * @link
     */
    private static $objectManager = null;

    /**
     *
     * @api
     * @param mixed $objectManager
     * @return mixed $objectManager
     * @link
     */
    public static function setObjectManager($objectManager)
    {
        return self::$objectManager = $objectManager;
    }

    /**
     *
     * @api
     * @return mixed $objectManager
     * @link
     */
    public static function getObjectManager()
    {
        return self::$objectManager;
    }

    /**
     *
     * @api
     * @param mixed $basePath
     * @return mixed $basePath
     * @link
     */
    public static function setBasePath($basePath)
    {
        return self::$basePath = $basePath;
    }

    /**
     *
     * @api
     * @return mixed $basePath
     * @link
     */
    public static function getBasePath()
    {
        return self::$basePath;
    }

    public static function setTemplateDir($templateDir)
    {
        self::$templateDir = $templateDir;
    }

    public static function getTemplateDir()
    {
        return self::$templateDir;
    }

    public static function getViewModel($config)
    {
        if ($config instanceof ViewModelInterface) {
            return $config;
        }
        //throw exception if not set
        if (!isset($config["viewModel"])) {
            throw new Exception(sprintf(self::ERROR_INVALID_VIEWMODEL_CONFIG));
        }
        return self::getView($config);
    }

    private static function getView($config)
    {
        $requestName = $config["viewModel"];
        $viewModelName = $requestName;

        $ViewModel = new $viewModelName($config, self::getObjectManager());
        if ($ViewModel->getTemplateDir() === null) {
            $ViewModel->setTemplateDir(self::getTemplateDir());
        }
        self::addView($ViewModel);
        $ViewModel->triggerEvent(EventTargetInterface::TRIGGER_INIT);
        return $ViewModel;
    }

    public static function addView(ViewModelInterface $viewModel)
    {
        $viewId = $viewModel->getId();
        if (isset(self::$viewModelPool[$viewId])) {
            throw new Exception(sprintf(self::ERROR_VIEWMODEL_DEFINED_ID, $viewId));
        }
        self::$viewModelPool[$viewId] = $viewModel;
    }

    public static function getViewById($viewId)
    {
        if (isset(self::$viewModelPool[$viewId])) {
            return self::$viewModelPool[$viewId];
        }
    }
}
