<?php

namespace Framework\ViewModel\Admin\Cms;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Layout\AdminPageLayout;

class ViewListViewModel extends CmsViewModel
{
    protected $template = '/template/admin/dashboard.html';
        
    protected $config = [
        'layout' => AdminPageLayout::class,
        'container' => [
            'Head' => [
                [
                    'viewModel' => 'Admin\NavbarViewModel',
                ],
            ],
            'Main' => [
                [
                    'viewModel' => View\ListViewModel::class,
                ]
            ]
        ],
    ];

    protected $data = [
        'title' => 'ビュー管理',
    ];
}