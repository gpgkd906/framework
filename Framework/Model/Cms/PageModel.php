<?php

namespace Framework\Model\Cms;

class PageModel extends AbstractCmsModel
{
    public function getEntities()
    {
        return parent::getController();
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
        $fileList = parent::getController();
        $find = null;
        foreach($fileList as $file) {
            if($file['nameHash'] === $identify) {
                $find = $file;
                break;
            }
        }
        if($find !== null) {
            $Ast = $this->getCodeService()->analysis($find['fullPath']);
        }
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
        var_dump($data);
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
