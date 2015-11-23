<?php

namespace Framework\Model\Cms;

use Framework\Model\AbstractModel;

class ViewModel extends AbstractModel
{
    
    public function getEntities()
    {
        $CodeService = $this->getServiceManager()->get('Service', 'CodeService');

        $viewDir = ROOT_DIR . 'Framework/ViewModel';
        $fullPathFlag = false;
        $fileList = $CodeService->scan($viewDir, [$viewDir . '/ViewModel', $viewDir . '/template'], $fullPathFlag);
        $entities = [];
        foreach($fileList as $folder => $list) {
            $entities[] = [
                'folder' => $folder,
                'list' => $list,
            ];
        }
        return $entities;
    }
    
}
