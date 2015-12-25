<?php

namespace Framework\Model\Cms;

class PageModel extends AbstractCmsModel
{
    public function getEntities()
    {
        return parent::scanController();
    }

    public function getEntity($identify = null)
    {
        if($identify === null){
            if($this->getIdentify()) {
                $identify = $this->getIdentify();
            } else {
                return null;
            }
        }
        return $this->getController($identify);
    }
    
    public function getController($identify = null)
    {
        $fileList = parent::scanController();
        $find = $this->findFile($identify, $fileList);
        if(!$find) {
            return false;
        }
        $Ast = $this->getCodeService()->analysis($find['fullPath']);
        $find['pid'] = $find['nameHash'];
        $find['url'] = $this->getUrl($find);
        $find['options']['model'] = $this->getModel();
        $find['options']['viewModel'] = $this->getViewModel();
        if($find['fileSize'] > 0) {
            $ActionAst = $Ast->getClass()->getMethod($find['action']);        
            if($ActionAst->getReturn()->isStaticCall()) {
                
            }
        }
        //var_Dump($find, $ActionAst->getReturn()->getNode()->expr);
        /* $fieldset->bind([ */
        /*     'pid' => $entity['nameHash'], */
        /*     'layout' => $entity['layout'], */
        /*     'model' => $entity['model'], */
        /*     'view' => $entity['view'], */        
        /*     'pageStatus' => $entity['pageStatus'], */
        /*     'authorizeType' => $entity['authorizeType'], */
        /*     'keyword' => '', */
        /*     'description' => '',             */
        /* ]); */
        
        return $find;
    }

    public function saveNewPage($data)
    {
        if(substr($data['url'], -1, 1) === '/') {
            return false;
        }
        $controller = [];
        $controllerDir = 'Framework/Controller/';
        $urlParts = array_map('ucfirst', explode('/', $data['url']));
        $name = array_pop($urlParts);
        $controller = $this->newControllerEntity($name, $urlParts);
        if(empty($data['model'])) {
            $model = $this->newModelEntity($name, $urlParts);
        } else {
            $model = $this->matchEntity($data['model'], parent::scanModel());
        }        
        if(empty($data['view'])) {
            $viewmodel = $this->newViewModelEntity($name, $urlParts);
        } else {
            $viewmodel = $this->matchEntity($data['view'], parent::scanViewModel());
        }
        $controller['model']     = $model;
        $controller['viewModel'] = $viewmodel;
        $controllerAst = $this->makeControllerAst($controller);
        $viewmodelAst = $this->makeViewModelAst($viewmodel);
        var_dump(
            //$controllerAst->toString()
            $viewmodelAst->toString()
            //$controller
            //$controller,
            , $model
            //$viewmodel,
            , $data
        );
    }
    
    private function getUrl($find)
    {
        $url = strtolower(str_replace('Controller.php', '', $find['file']));
        
        if(isset($find['action']) && $find['action'] !== 'index') {
            $url = $url . '/' . $find['action'];
        }
        if($url[0] === '/') {
            $url = substr($url, 1);
        }
        return $url;
    }

    public function makeControllerAst($controller)
    {
        $model = $controller['model'];
        $viewmodel = $controller['viewModel'];
        $Ast = $this->getCodeService()->newAst();
        $Ast->setNamespace($controller['namespace']);
        $Ast->getNamespace()->appendUse($this->getAbstractController());
        $Ast->getNamespace()->appendUse($this->getViewModelManager());
        $Ast->getNamespace()->appendUse($model['namespace'] . '\\' . $model['name']);
        $Ast->getNamespace()->appendUse($viewmodel['namespace'] . '\\' . $viewmodel['name']);
        $Ast->setClass($controller['name']);
        $Ast->getClass()->extend('AbstractController');
        $Ast->getClass()->appendMethod('index');        
        $Ast->getClass()->getMethod('index')->setReturn("ViewModelManager::getViewModel([ 'viewModel' => " . $viewmodel['name'] . "::class ]);");
        return $Ast;
    }
    
    public function makeViewModelAst($viewmodel)
    {
        $Ast = $this->getCodeService()->newAst();
        $Ast->setNamespace($viewmodel['namespace']);
        $Ast->getNamespace()->appendUse($this->getAbstractViewModel());
        $Ast->getNamespace()->appendUse($this->getEventTargetInterface());
        $Ast->setClass($viewmodel['name']);
        $Ast->getClass()->extend('AbstractViewModel');
        $Ast->getClass()->appendImplement('EventTargetInterface');
        $Ast->getClass()->appendTrait($this->getEventTargetTrait());
        $Ast->getClass()->appendProperty('template', 123);
        $Ast->getClass()->getProperty('template')->setAccess('private');
        $Ast->getClass()->getProperty('template')->setStatic(true);
        $Ast->getClass()->getProperty('template')->setValue([1, 2, 3]);
        return $Ast;
    }
}
