<?php
declare(strict_types=1);

namespace Project\Core\Admin\View\ViewModel;

use Std\ViewModel\AbstractViewModel;
use Project\Core\Admin\View\Layout\AdminPageLayout;

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
