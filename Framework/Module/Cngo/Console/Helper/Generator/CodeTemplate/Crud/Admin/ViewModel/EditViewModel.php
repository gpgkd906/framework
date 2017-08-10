<?php
declare(strict_types=1);

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
        $formData = $this->getForm()->getData();
        if (isset($formData['{entity}'])) {
            $data['{entity}'] = array_merge($data['{entity}'], $formData['{entity}']);
        }
        $this->getForm()->setData($data);
    }
}
