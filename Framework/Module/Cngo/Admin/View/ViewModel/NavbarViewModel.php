<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareInterface;

class NavbarViewModel extends AbstractViewModel implements AuthenticationAwareInterface
{
    use \Framework\Module\Cngo\AdminUser\Authentication\AuthenticationAwareTrait;

    protected $template = '/template/navbar.phtml';

    protected $config = [
        'container' => [
            'Side' => [
                [ 'viewModel' => SidemenuViewModel::class ],
            ],
        ],
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
