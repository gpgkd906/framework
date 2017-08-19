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
        $formData = $this->getForm()->getData();
        if (isset($formData['adminUser'])) {
            $data['adminUser'] = array_merge($data['adminUser'], $formData['adminUser']);
        }
        $this->getForm()->setData($data);
    }

    public function getFieldset()
    {
        $this->fieldset['adminUser']['password']['inputSpecification'] = null;
        $this->fieldset['adminUser']['passwordConfirm']['inputSpecification'] = null;
        return $this->fieldset;
    }
}
