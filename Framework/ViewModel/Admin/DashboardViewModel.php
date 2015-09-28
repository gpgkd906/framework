<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class DashboardViewModel extends AbstractViewModel
{
    protected $template = '/template/admin/dashboard.html';
    
    protected $config = [
        'layout' => AdminLayout::class,
        'container' => [
            'Head' => [
                [
                    'viewModel' => 'Admin\NavbarViewModel',
                ],
            ],
            'Main' => [
                [
                    'viewModel' => 'Admin\SummaryViewModel',
                ],
                [
                    'viewModel' => 'Admin\ChartViewModel',
                ],
            ],

        ],
    ];

    protected $data = [
        'title' => 'Dashboard',
    ];
}