<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\View\ViewModel\Users;

use Std\ViewModel\AbstractViewModel;
use Project\Core\Admin\View\Layout\AdminPageLayout;

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
        self::TRIGGER_BEFORE_RENDER => 'onRender',
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
