<?php
declare(strict_types=1);

namespace Framework\Model\Cms;

use Framework\Model\AbstractModel;

class ViewModel extends AbstractCmsModel
{
    public function getEntities()
    {
        $dir = ROOT_DIR . 'Framework/ViewModel';
        $fileList = $this->getCodeService()->scan($dir, [$dir . '/ViewModel', $dir . '/template']);
        return $fileList;       
    }
}
