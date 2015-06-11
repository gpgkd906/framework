<?php

namespace Framework\Core\ViewModel;

use Framework\Core\Interfaces\ViewModelManagerInterface;
use Framework\Core\Interfaces\ViewModelInterface;
use Exception;

class ViewModelManager implements ViewModelManagerInterface
{
    const ERROR_INVALID_VIEWMODEL_CONFIG
        = "error: invalid viewmodel config";
    const ERROR_INVALID_VIEWMODEL = "error: invalid viewmodelname: %s";
    const ERROR_INVALID_TEMPLATE_VIEWMODEL = "error: invalid template viewModel";
    const ERROR_VIEWMODEL_DEFINED_ID = "error: viewId [%s] was defined before, change some new ID";

    static private $defaultViewModel = "Framework\Core\ViewModel\ViewModel";
    static private $templateViewModel = "Framework\Core\ViewModel\TemplateViewModel";
    static private $alias = [];
    static private $viewModelPool = [];
    //
    static private $namespace = null;
    static private $templateDir = null;
    
    static public function setNamespace($namespace) {
        self::$namespace = $namespace;
    }

    static public function getNamespace()
    {
        return self::$namespace;
    }

    static public function setTemplateDir($templateDir)
    {
        self::$templateDir = $templateDir;
    }

    static public function getTemplateDir()
    {
        return self::$templateDir;
    }

    static public function getViewModel($config)
    {
        if($config instanceof ViewModelInterface) {
            return $config;
        }
        //set default viewmodel if not set
        if(!isset($config["viewModel"])) {
            $config["viewModel"] = self::$defaultViewModel;
        }
        return self::getView($config);
    }
    
    static private function getView($config)
    {
        $viewModelName = $config["viewModel"];
        if($aliasViewModel = self::getAlias($viewModelName)) {
            $viewModelName = $aliasViewModel;
        }
        if(!class_exists($viewModelName)) {
            $viewModelName = self::$namespace . "\\" . $viewModelName;
            if(!class_exists($viewModelName)) {
                throw new Exception(sprintf(self::ERROR_INVALID_VIEWMODEL, $viewModelName));
            }
            self::setAlias($config["viewModel"], $viewModelName);
        }
        $ViewModel = new $viewModelName($config);
        if($ViewModel->getTemplateDir() === null) {
            $ViewModel->setTemplateDir(self::getTemplateDir());
        }
        return $ViewModel;
    }

    static public function addView(ViewModelInterface $viewModel)
    {
        $viewId = $viewModel->getId();
        if(isset(self::$viewModelPool[$viewId])) {
            throw new Exception(sprintf(self::ERROR_VIEWMODEL_DEFINED_ID, $viewId));
        }
        self::$viewModelPool[$viewId] = $viewModel;
    }

    static public function getViewById($viewId)
    {
        if(isset(self::$viewModelPool[$viewId])) {
            return self::$viewModelPool[$viewId];
        }
    }

    static public function setAlias($alias, $viewModelName)
    {
        self::$alias[$alias] = $viewModelName;
    }

    static public function getAlias($alias)
    {
        return isset(self::$alias[$alias]) ? self::$alias[$alias] : null;
    }

    static public function setTemplateViewModel($viewModelName)
    {
        $this->templateViewModel = $viewModelName;
    }

    static public function getTemplateViewModel()
    {
        return $this->templateViewModel;
    }

    static public function setDefaultViewModel($viewModelName)
    {
        self::$defaultViewModel = $viewModelName;
    }

    static public function getDefaultViewModel()
    {
        return self::$defaultViewModel;
    }
}