<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\ModelManager\ModelManagerAwareInterface;
use Framework\Module\Cngo\Admin\Model\AdminUser;

class NavbarViewModel extends AbstractViewModel implements
    ModelManagerAwareInterface
{
    use \Framework\ModelManager\ModelManagerAwareTrait;

    protected $template = '/template/navbar.phtml';

    private $_model;

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }

    public function getModel()
    {
        if ($this->_model === null) {
            $this->_model = $this->getModelManager()->getModel(AdminUser::class);
        }
        return $this->_model;
    }
}
