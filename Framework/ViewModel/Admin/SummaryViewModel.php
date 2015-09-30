<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class SummaryViewModel extends AbstractViewModel
{
    protected $template = '/template/admin/component/summary.html';

    public $listeners = [
        'Render' => 'checkDashboardStatus',
    ];

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
