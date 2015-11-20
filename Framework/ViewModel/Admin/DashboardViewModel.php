<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Layout\AdminPageLayout;

class DashboardViewModel extends AbstractViewModel
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
                    'viewModel' => 'Admin\SummaryViewModel',
                ],
                [
                    'viewModel' => 'Admin\TopChartViewModel',
                ],
            ],            
        ],
        'script' => [
            '/sbadmin2/js/morris-data.js'
        ]
    ];

    protected $data = [
        'title' => 'Dashboard',
    ];

    public $listeners = [
        'Render' => 'onRender',
    ];

    public function onRender()
    {
        $this->getLayout()->setPageVar('title', $this->getData()['title']);
    }
}