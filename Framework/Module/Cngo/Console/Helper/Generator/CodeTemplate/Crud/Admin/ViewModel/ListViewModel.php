<?php
declare(strict_types=1);

namespace Framework\Module\{Module}\View\ViewModel{Namespace};

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class ListViewModel extends AbstractViewModel
{
    protected $template = '/template{namespace}/list.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

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
        $data['{entity}'] = array_map(function (${Entity}) {
            return ${Entity}->toArray();
        }, $data['{entity}']);
        $this->setData($data);
    }
}
