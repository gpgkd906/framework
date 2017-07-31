<?php

namespace Framework\Module\Cms\Admin\View\ViewModel\Section;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class EditViewModel extends RegisterViewModel
{
    protected $template = '/template/section/edit.phtml';

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
        $data['controllerGroup'] = $data['controllerGroup']->toArray();
        $formData = $this->getForm()->getData();
        if (isset($formData['controllerGroup'])) {
            $data['controllerGroup'] = array_merge($data['controllerGroup'], $formData['controllerGroup']);
        }
        $this->getForm()->setData($data);
    }
}
