<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareInterface;
use Framework\ModelManager\ModelManagerAwareInterface;
use Framework\Module\Cngo\Admin\Model\User;

class NavbarViewModel extends AbstractViewModel implements
    AuthenticationAwareInterface,
    ModelManagerAwareInterface
{
    use \Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareTrait;
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
            $this->model = $this->getModelManager()->getModel(User::class);
        }
        return $this->model;
    }
}
