<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel\Users;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class RegisterViewModel extends AbstractViewModel
{
    protected $template = '/template/users/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}
