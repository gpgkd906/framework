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
    static private $templateViewModel = "Framework\Core\ViewModel\TemplateViewModel";
    static private $alias = [];
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
        //viewModel?
        if(isset($config["viewModel"])) {
            return self::getView($config);
        } else {
            throw new Exception(self::ERROR_INVALID_VIEWMODEL_CONFIG);
        }
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
        $ViewModel = new $viewModelName;
        if($ViewModel->getTemplateDir() === null) {
            $ViewModel->setTemplateDir(self::getTemplateDir());
        }
        return $ViewModel;
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
}