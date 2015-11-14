<?php

namespace Framework\ViewModel\Admin\Cms;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Admin\AdminPageLayout;


class PageListViewModel extends CmsViewModel
{
    
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
                    'viewModel' => Component\PageListViewModel::class,
                ]
            ]
        ],
    ];

    protected $data = [
        'title' => 'Cms/PageList',
    ];
}