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
        $controller['name'] = $name . 'Controller';
        $controller['dir']  = ROOT_DIR . $controllerDir . join('/', $urlParts) . '/';
        $controller['path'] = $controller['dir'] . $controller['name'] . '.php';
        $controller['namespace'] = str_replace('/', '\\', $controllerDir . join('/', $urlParts));
        if(empty($data['model'])) {
            $model = $this->newModelEntity($name, $urlParts);
        } else {
            $model = $this->matchEntity($data['model'], parent::scanModel());
        }
        if(empty($data['view'])) {
            $viewmodel = $this->newModelEntity($name, $urlParts);
        } else {
            $viewmodel = $this->matchEntity($data['view'], parent::scanViewModel());
        }
        var_dump(
            $controller,
            $model,
            $viewmodel,
            $data
        );
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

}
