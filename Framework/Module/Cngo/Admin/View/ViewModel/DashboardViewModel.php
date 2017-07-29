<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

class DashboardViewModel extends AbstractViewModel
{
    protected $template = '/template/dashboard.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
        'container' => [
            'Main' => [
                [
                    'viewModel' => SummaryViewModel::class,
                ],
            ],
        ],
        'script' => [
            '/sbadmin2/js/morris-data.js'
        ]
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
