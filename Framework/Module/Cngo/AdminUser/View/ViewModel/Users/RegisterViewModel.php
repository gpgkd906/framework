<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\View\ViewModel\Users;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;
use Framework\Module\Cngo\AdminUser\Fieldset\AdminUserFieldset;

class RegisterViewModel extends FormViewModel
{
    protected $template = '/template/users/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $fieldset = [
        AdminUserFieldset::class
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }
}
