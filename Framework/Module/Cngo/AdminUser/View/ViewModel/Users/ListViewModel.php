<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\View\ViewModel\Users;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class ListViewModel extends AbstractViewModel
{
    protected $template = '/template/users/list.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
        'script' => [
            '/../js/dataTables/jquery.dataTables.min.js',
            '/../js/dataTables/dataTables.bootstrap.min.js',
            '/../js/dataTables/dataTables.responsive.js'
        ]
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
        // var_dump(__METHOD__);
        $data = $this->getData();
        $data['users'] = array_map(function ($AdminUser) {
            return $AdminUser->toArray();
        }, $data['users']);
        $this->setData($data);
    }
}
