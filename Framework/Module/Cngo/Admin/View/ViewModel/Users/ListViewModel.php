<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel\Users;

use Framework\ViewModel\ViewModel\AbstractViewModel;
class ListViewModel extends AbstractViewModel
{
    protected $template = '/template/users/list.phtml';
    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}
