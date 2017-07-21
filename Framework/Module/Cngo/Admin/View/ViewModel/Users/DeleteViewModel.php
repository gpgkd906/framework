<?php 

namespace Framework\Module\Cngo\Admin\View\ViewModel\Users;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
class DeleteViewModel extends AbstractViewModel
{
    protected $template = '/template/users/delete.phtml';
    protected $config = ['layout' => '\\Framework\\Module\\Cngo\\Admin\\View\\Layout\\AdminPageLayout'];
    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}