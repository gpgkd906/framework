<?php 

namespace Framework\Module\Cngo\Admin\View\ViewModel\Users;

use Framework\ViewModel\ViewModel\AbstractViewModel;
class RegisterViewModel extends AbstractViewModel
{
    protected $template = '/template/users/register.phtml';
    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}