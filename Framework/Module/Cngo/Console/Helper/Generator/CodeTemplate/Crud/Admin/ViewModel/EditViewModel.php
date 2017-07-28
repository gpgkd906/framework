<?php

namespace Framework\Module\{Module}\View\ViewModel{Namespace};

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class EditViewModel extends RegisterViewModel
{
    protected $template = '/template{namespace}/edit.phtml';

    public $listeners = [
        'Render' => 'onRender',
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..{ns}';
    }

    public function onRender()
    {
        $data = $this->getData();
        $data['{entity}'] = $data['{entity}']->toArray();
        $this->getForm()->setData($data);
    }
}
