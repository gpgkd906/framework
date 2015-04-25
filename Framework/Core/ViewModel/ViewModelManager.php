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

    static public function setNamespace($namespace) {
        self::$namespace = $namespace;
    }

    static public function getViewModel($config)
    {
        //viewModel?
        if(isset($config["viewModel"])) {
            return self::getView($config);
        } elseif($config["tpl"]) {
            return self::getTplView($config);
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
        }
        if(!class_exists($viewModelName)) {
            throw new Exception(sprintf(self::ERROR_INVALID_VIEWMODEL, $viewModelName));
        }
        return new $viewModelName;
    }

    static private function getTplView($config)
    {
        $viewModelName = self::$templateViewModel;
        if(!class_exists($viewModelName)) {
            throw new Exception(self::ERROR_INVALID_TEMPLATE_VIEWMODEL);            
        }
        $TemplateViewModel = new $viewModelName;
        $TemplateViewModel->setConfig($config);
        return $TemplateViewModel;
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