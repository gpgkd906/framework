<?php

namespace Framework\ViewModel\Admin\Cms;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Layout\AdminPageLayout;

class PageListViewModel extends CmsViewModel
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
                    'viewModel' => Page\ListViewModel::class,
                ]
            ]
        ],
    ];

    protected $data = [
        'title' => 'ページ一覧',
    ];
}