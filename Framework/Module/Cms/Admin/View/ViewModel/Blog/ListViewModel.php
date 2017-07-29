<?php

namespace Framework\Module\Cms\Admin\View\ViewModel\Blog;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class ListViewModel extends AbstractViewModel
{
    protected $template = '/template/blog/list.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

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
        $data['blog'] = array_map(function ($Blog) {
            return $Blog->toArray();
        }, $data['blog']);
        $this->setData($data);
    }
}
