<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Layout\AdminPageLayout;

class CustomerViewModel extends AbstractViewModel
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
                    'viewModel' => 'Admin\Customer\ListViewModel',
                ]
            ],            
        ],
    ];

    protected $data = [
        'title' => '会員管理',
    ];
}