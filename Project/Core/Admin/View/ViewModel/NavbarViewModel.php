<?php
declare(strict_types=1);

namespace Project\Core\Admin\View\ViewModel;

use Std\ViewModel\AbstractViewModel;
use Std\ModelManager\ModelManagerAwareInterface;
use Project\Core\Admin\Model\AdminUser;

class NavbarViewModel extends AbstractViewModel implements
    ModelManagerAwareInterface
{
    use \Std\ModelManager\ModelManagerAwareTrait;

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
