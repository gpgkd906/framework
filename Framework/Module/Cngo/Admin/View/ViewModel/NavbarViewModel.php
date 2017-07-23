<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\Authentication\AuthenticationAwareInterface;

class NavbarViewModel extends AbstractViewModel implements AuthenticationAwareInterface
{
    use \Framework\Module\Cngo\Admin\Authentication\AuthenticationAwareTrait;

    protected $template = '/template/component/navbar.phtml';

    public $listeners = [
        parent::TRIGGER_INIT => 'onInit',
    ];

    protected $config = [
        'container' => [
            'Side' => [
                [ 'viewModel' => SidemenuViewModel::class ],
            ],
        ],
    ];

    public function onInit()
    {
        $this->setData($this->getAuthentication()->getIdentity());
    }

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
