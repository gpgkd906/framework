<?php

namespace Framework\Module\Cngo\Admin\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;

class SummaryViewModel extends AbstractViewModel
{
    protected $template = '/template/summary.phtml';

    public $listeners = [
        'Render' => 'checkDashboardStatus',
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }

    public function checkDashboardStatus()
    {
        $data = [
            'summary' => [
                'comment' => [
                    'panel' => 'primary',
                    'icon' => 'fa-comments',
                    'new' => 26,
                    'message' => 'New Comments!',
                ],
                'task' => [
                    'panel' => 'green',
                    'icon' => 'fa-tasks',
                    'new' => 12,
                    'message' => 'New Tasks!',
                ],
                'order' => [
                    'panel' => 'yellow',
                    'icon' => 'fa-shopping-cart',
                    'new' => 124,
                    'message' => 'New Orders!',
                ],
                'tickets' => [
                    'panel' => 'red',
                    'icon' => 'fa-support',
                    'new' => 13,
                    'message' => 'Support Tickets!',
                ],
            ]
        ];
        $this->setData($data);
    }
}
