<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class DashboardViewModel extends AbstractViewModel
{
    protected $template = '/template/dashboard.html';

    protected $config = [
        'layout' => AdminPageLayout::class,
        'container' => [
            'Head' => [
                [
                    'viewModel' => NavbarViewModel::class,
                ],
            ],
            'Main' => [
                [
                    'viewModel' => SummaryViewModel::class,
                ],
                [
                    'viewModel' => TopChartViewModel::class,
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

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }

    public function onRender()
    {
        $this->getLayout()->setPageVar('title', $this->getData()['title']);
    }
}
