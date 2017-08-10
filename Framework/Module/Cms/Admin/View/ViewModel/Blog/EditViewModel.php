<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\View\ViewModel\Blog;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class EditViewModel extends RegisterViewModel
{
    protected $template = '/template/blog/edit.phtml';

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
        $data['blog'] = $data['blog']->toArray();
        $this->getForm()->setData($data);
    }
}
