<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel\Users;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class ListViewModel extends AbstractViewModel
{
    protected $template = '/template/users/list.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}
