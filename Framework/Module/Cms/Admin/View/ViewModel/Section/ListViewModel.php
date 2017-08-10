<?php
declare(strict_types=1);

namespace Framework\Module\Cms\Admin\View\ViewModel\Section;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class ListViewModel extends AbstractViewModel
{
    protected $template = '/template/section/list.phtml';

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
        $data['controllerGroup'] = array_map(function ($ControllerGroup) {
            return $ControllerGroup->toArray();
        }, $data['controllerGroup']);
        $this->setData($data);
    }
}
