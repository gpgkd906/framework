<?php

namespace Framework\Module\Cms\Admin\View\ViewModel\Page;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class ListViewModel extends AbstractViewModel
{
    protected $template = '/template/page/list.phtml';

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
        $data['controller'] = array_map(function ($Controller) {
            return $Controller->toArray();
        }, $data['controller']);
        $this->setData($data);
    }
}
