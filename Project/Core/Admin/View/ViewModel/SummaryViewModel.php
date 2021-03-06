<?php
declare(strict_types=1);

namespace Project\Core\Admin\View\ViewModel;

use Std\ViewModel\AbstractViewModel;

class SummaryViewModel extends AbstractViewModel
{
    protected $template = '/template/summary.phtml';

    public $listeners = [
        self::TRIGGER_BEFORE_RENDER => 'checkDashboardStatus',
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
