<?php

namespace Framework\Model\Cms;

use Framework\Model\AbstractModel;

class PageModel extends AbstractModel
{
    /**
     *
     * @api
     * @var mixed $identify 
     * @access private
     * @link
     */
    private $identify = null;
    
    public function getEntities()
    {
        $CodeService = $this->getServiceManager()->get('Service', 'CodeService');
        $controllerDir = ROOT_DIR . 'Framework/Controller';
        $fileList = $CodeService->scan($controllerDir, $controllerDir . '/Controller');
        return $fileList;
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
        $CodeService = $this->getServiceManager()->get('Service', 'CodeService');
        $controllerDir = ROOT_DIR . 'Framework/Controller';
        $fileList = $CodeService->scan($controllerDir, $controllerDir . '/Controller');
        $find = null;
        foreach($fileList as $file) {
            if($file['nameHash'] === $identify) {
                $find = $file;
                break;
            }
        }
        if($find !== null) {
            $find = $CodeService->analysis($find);
        }
        return $find;
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
