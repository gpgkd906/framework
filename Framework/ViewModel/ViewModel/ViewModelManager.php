<?php

namespace Framework\ViewModel\ViewModel;

use Framework\EventManager\EventTargetInterface;
use Exception;

class ViewModelManager implements ViewModelManagerInterface
{
    const ERROR_INVALID_VIEWMODEL_CONFIG = "error: invalid viewmodel config";
    const ERROR_INVALID_VIEWMODEL = "error: invalid viewmodelname: %s";
    const ERROR_INVALID_TEMPLATE_VIEWMODEL = "error: invalid template viewModel";
    const ERROR_VIEWMODEL_DEFINED_ID = "error: viewId [%s] was defined before, change some new ID";

    private static $defaultViewModel = "Framework\Core\ViewModel\ViewModel";
    private static $templateViewModel = "Framework\Core\ViewModel\TemplateViewModel";
    private static $alias = [];
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
        //set default viewmodel if not set
        if (!isset($config["viewModel"])) {
            $config["viewModel"] = self::$defaultViewModel;
        }
        return self::getView($config);
    }

    private static function getView($config)
    {
        $requestName = $config["viewModel"];
        $viewModelName = $requestName;
        if ($aliasViewModel = self::getAlias($viewModelName)) {
            $viewModelName = $aliasViewModel;
        }
        if (!class_exists($viewModelName)) {
            //IndexViewModelで書く場合は上層に見つかる
            $viewModelName = self::$namespace . "\\" . $viewModelName;
            //Indexで書く場合はは下層に見つかる
            if (!class_exists($viewModelName)) {
                $viewModelName = $viewModelName . '\\' . $requestName . 'ViewModel';
            }
            if (!class_exists($viewModelName)) {
                throw new Exception(sprintf(self::ERROR_INVALID_VIEWMODEL, $viewModelName));
            }
            self::setAlias($requestName, $viewModelName);
        }
        $ViewModel = new $viewModelName($config, self::getObjectManager());
        if ($ViewModel->getTemplateDir() === null) {
            $ViewModel->setTemplateDir(self::getTemplateDir());
        }
        if ($ViewModel->getLayout() !== null) {
            $ViewModel->getLayout()->setPageVars($ViewModel->getData());
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

    public static function setAlias($alias, $viewModelName)
    {
        self::$alias[$alias] = $viewModelName;
    }

    public static function getAlias($alias)
    {
        return isset(self::$alias[$alias]) ? self::$alias[$alias] : null;
    }

    public static function setTemplateViewModel($viewModelName)
    {
        $this->templateViewModel = $viewModelName;
    }

    public static function getTemplateViewModel()
    {
        return $this->templateViewModel;
    }

    public static function setDefaultViewModel($viewModelName)
    {
        self::$defaultViewModel = $viewModelName;
    }

    public static function getDefaultViewModel()
    {
        return self::$defaultViewModel;
    }
}
