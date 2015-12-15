<?php

namespace Framework\Model\Cms;

use Framework\Model\AbstractModel;

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
        if($this->codeService === null) {
            $this->codeService = $this->getServiceManager()->get('Service', 'CodeService');
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
        foreach($fileList as $file) {
            if($file['nameHash'] === $identify) {
                return $file;
            }
        }
    }

    public function getAst($identify, $fileList)
    {
        $find = null;
        foreach($fileList as $file) {
            if($file['nameHash'] === $identify) {
                return $this->getCodeService()->analysis($file['fullPath']);
            }
        }
    }

    public function matchEntity($identify, $fileList)
    {
        $Ast = $this->getAst($identify, $fileList);
        $file = $this->findFile($identify, $fileList);
        if($Ast) {
            return [
                'name' => $Ast->getClass()->getName(),
                'namespace' => $Ast->getNamespace()->getNamespace(),
                'dir' => $file['dir'] . '/',
                'path' => $file['fullPath'],
            ];
        }
    }
    
    private function getFileEntities($dir, $exclude = null)
    {
        $temps = $this->getCodeService()->scan($dir, $exclude);
        $results = [];
        foreach($temps as $file) {
            if($file['fileSize'] < 0) {
                continue;
            }
            if(strpos($file['file'], 'Abstract') !== false) {
                continue;
            }
            if(strpos($file['file'], 'Interface') !== false) {
                continue;
            }
            if(strpos($file['file'], 'Trait') !== false) {
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
}