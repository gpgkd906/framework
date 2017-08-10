<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\View\ViewModel\Users;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class EditViewModel extends RegisterViewModel
{
    protected $template = '/template/users/edit.phtml';

    public $listeners = [
        'Render' => 'onRender',
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/../..';
    }

    public function onRender()
    {
        $data = $this->getData();
        $data['adminUser'] = $data['adminUser']->toArray();
        unset($data['adminUser']['password']);
        $this->getForm()->setData($data);
    }
}
