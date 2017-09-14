<?php
declare(strict_types=1);

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
        ]
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
