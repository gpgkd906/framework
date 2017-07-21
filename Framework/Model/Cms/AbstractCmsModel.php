<?php

namespace Framework\Model\Cms;

use Framework\Model\AbstractModel;
use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModelManager;
use Framework\ViewModel\AbstractViewModel;
use Framework\Event\Event\EventTargetInterface;
use Framework\Event\Event\EventTargetTrait;

abstract class AbstractCmsModel extends AbstractModel
{
    /**
     *
     * @api
     * @var mixed $codeService 
     * @access private
     * @link
     */
    private $codeService = null;
    
    /**
     *
     * @api
     * @var mixed $identify 
     * @access private
     * @link
     */
    private $identify = null;
    
    /**
     * 
     * @api
     * @param mixed $codeService
     * @return mixed $codeService
     * @link
     */
    public function setCodeService ($codeService)
    {
        return $this->codeService = $codeService;
    }

    /**
     * 
     * @api
     * @return mixed $codeService
     * @link
     */
    public function getCodeService ()
    {
        if ($this->codeService === null) {
            $this->codeService = $this->getObjectManager()->get('Service', 'CodeService');
        }
        return $this->codeService;
    }
    
    public function scanController()
    {
        $dir = ROOT_DIR . 'Framework/Controller';
        return $this->getCodeService()->scan($dir, $dir . '/Controller');
    }
    
    public function scanModel()
    {
        $dir = ROOT_DIR . 'Framework/Model';
        return $this->getCodeService()->scan($dir, $dir . '/Model');
    }

    public function getModel()
    {
        $dir = ROOT_DIR . 'Framework/Model';
        return $this->getFileEntities($dir, $dir . '/Model');
    }

    public function scanViewModel()
    {
        $dir = ROOT_DIR . 'Framework/ViewModel';
        return $this->getCodeService()->scan($dir, [$dir . '/ViewModel', $dir . '/template', $dir . '/Admin/Component']);
    }

    public function getViewModel()
    {
        $dir = ROOT_DIR . 'Framework/ViewModel';
        return $this->getFileEntities($dir, [$dir . '/ViewModel', $dir . '/template', $dir . '/Admin/Component']);
    }

    public function findFile($identify, $fileList)
    {
        $find = null;
        foreach ($fileList as $file) {
            if (isset($file['nameHash']) && $file['nameHash'] === $identify) {
                return $file;
            }
        }
    }

    public function getAst($identify, $fileList)
    {
        $find = null;
        foreach ($fileList as $file) {
            if (isset($file['nameHash']) && $file['nameHash'] === $identify) {
                return $this->getCodeService()->analysis($file['fullPath']);
            }
        }
    }

    public function matchEntity($identify, $fileList)
    {
        $Ast = $this->getAst($identify, $fileList);
        $file = $this->findFile($identify, $fileList);
        if ($Ast) {
            return [
                'name' => $Ast->getClass()->getName(),
                'namespace' => $Ast->getNamespace()->getNamespace(),
                'dir' => $file['dir'] . '/',
                'path' => $file['fullPath'],
            ];
        }
    }    

    public function newControllerEntity($name, $namespaceparts)
    {
        $controllerDir = 'Framework/Controller/';
        $controller = [];
        $controller['name'] = $name . 'Controller';
        $controller['namespace'] = str_replace('/', '\\', $controllerDir . join('/', $namespaceparts));
        $controller['dir']  = ROOT_DIR . $controllerDir . join('/', $namespaceparts) . '/';
        $controller['path'] = $controller['dir'] . $controller['name'] . '.php';
        return $controller;
    }
    
    public function newModelEntity($name, $namespaceparts)
    {
        $modelDir = 'Framework/Model/';
        $model = [];
        $model['name'] = $name . 'Model';
        $model['namespace'] = str_replace('/', '\\', $modelDir . join('/', $namespaceparts));
        $model['dir']  = ROOT_DIR . $modelDir . join('/', $namespaceparts) . '/';
        $model['path'] = $model['dir'] . $model['name'] . '.php';
        return $model;
    }

    public function newViewModelEntity($name, $namespaceparts)
    {
        $viewDir = 'Framework/ViewModel/';
        $view = [];
        $view['name'] = $name . 'ViewModel';
        $view['namespace'] = str_replace('/', '\\', $viewDir . join('/', $namespaceparts));
        $view['dir']  = ROOT_DIR . $viewDir . join('/', $namespaceparts) . '/';
        $view['path'] = $view['dir'] . $view['name'] . '.php';
        return $view;
    }
    
    private function getFileEntities($dir, $exclude = null)
    {
        $temps = $this->getCodeService()->scan($dir, $exclude);
        $results = [];
        foreach ($temps as $file) {
            if ($file['fileSize'] < 0) {
                continue;
            }
            if (strpos($file['file'], 'Abstract') !== false) {
                continue;
            }
            if (strpos($file['file'], 'Interface') !== false) {
                continue;
            }
            if (strpos($file['file'], 'Trait') !== false) {
                continue;
            }
            $results[$file['file']] = $file['nameHash'];
        }
        return $results;
    }
    
    /**
     * 
     * @api
     * @param mixed $identify
     * @return mixed $identify
     * @link
     */
    public function setIdentify ($identify)
    {
        return $this->identify = $identify;
    }

    /**
     * 
     * @api
     * @return mixed $identify
     * @link
     */
    public function getIdentify ()
    {
        return $this->identify;
    }

    public function getAbstractController()
    {
        return AbstractController::class;
    }

    public function getAbstractViewModel()
    {
        return AbstractViewModel::class;
    }

    public function getViewModelManager()
    {
        return ViewModelManager::class;
    }

    public function getEventTargetInterface()
    {
        return EventTargetInterface::class;
    }

    public function getEventTargetTrait()
    {
        return EventTargetTrait::class;
    }
}