<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\View\ViewModel\Page;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class EditViewModel extends RegisterViewModel
{
    protected $template = '/template/page/edit.phtml';

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
        $data['controller'] = $data['controller']->toArray();
        $formData = $this->getForm()->getData();
        if (isset($formData['controller'])) {
            $data['controller'] = array_merge($data['controller'], $formData['controller']);
        }
        $this->getForm()->setData($data);
    }
}
