<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\View\ViewModel\Users;

use Std\ViewModel\FormViewModel;
use Project\Core\Admin\View\Layout\AdminPageLayout;
use Project\Core\AdminUser\Fieldset\AdminUserFieldset;

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
