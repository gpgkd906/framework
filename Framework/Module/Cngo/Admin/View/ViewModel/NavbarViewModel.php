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

    protected $config = [
        'container' => [
            'Side' => [
                [ 'viewModel' => SidemenuViewModel::class ],
            ],
        ],
    ];

    private $model;

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }

    public function getModel()
    {
        if ($this->model === null) {
            $this->model = $this->getModelManager()->getModel(AdminUser::class);
        }
        return $this->model;
    }
}
