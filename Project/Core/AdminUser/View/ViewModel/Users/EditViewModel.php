<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\View\ViewModel\Users;

use Std\ViewModel\AbstractViewModel;
use Project\Core\Admin\View\Layout\AdminPageLayout;
use Project\Core\AdminUser\Fieldset\AdminUserForEditFieldset;

class EditViewModel extends RegisterViewModel
{
    protected $template = '/template/users/edit.phtml';

    public $listeners = [
        self::TRIGGER_BEFORE_RENDER => 'onRender',
    ];

    protected $fieldset = [
        AdminUserForEditFieldset::class
    ];

    public function onRender()
    {
        $data = $this->getData();
        $data['adminUser'] = $data['adminUser']->toArray();
        unset($data['adminUser']['password']);
        $formData = $this->getForm()->getData();
        if (isset($formData['adminUser'])) {
            $data['adminUser'] = array_merge($data['adminUser'], $formData['adminUser']);
        }
        $this->getForm()->setData($data);
    }

    public function getFieldset()
    {
        return $this->fieldset;
    }
}
