<?php

namespace Framework\Module\Cngo\AdminTop\View\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class TopChartViewModel extends AbstractViewModel
{
    protected $template = '/template/component/topchart.html';

    protected $config = [
        'container' => [
            'Sub' => [
                ['viewModel' => TimelineViewModel::class ],
            ],
        ],
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
        $data = [
            'barChart' => [
                'header' => ['#', 'Date', 'Time', 'Amount'],
                'datas' => [
                    [
                        '#' => 3326,
                        'Date' => '10/21/2013',
                        'Time' => '3:29 PM',
                        'Amount' => '$321.33',
                    ],
                    [
                        '#' => 3325,
                        'Date' => '10/21/2013',
                        'Time' => '3:20 PM',
                        'Amount' => '$234.34',
                    ],
                    [
                        '#' => 3324,
                        'Date' => '10/21/2013',
                        'Time' => '3:03 PM',
                        'Amount' => '$724.17',
                    ],
                    [
                        '#' => 3323,
                        'Date' => '10/21/2013',
                        'Time' => '3:00 PM',
                        'Amount' => '$23.71',
                    ],
                    [
                        '#' => 3322,
                        'Date' => '10/21/2013',
                        'Time' => '2:49 PM',
                        'Amount' => '$8345.23',
                    ],
                    [
                        '#' => 3321,
                        'Date' => '10/21/2013',
                        'Time' => '2:23 PM',
                        'Amount' => '$245.12',
                    ],
                    [
                        '#' => 3320,
                        'Date' => '10/21/2013',
                        'Time' => '2:15 PM',
                        'Amount' => '$5663.54',
                    ],
                    [
                        '#' => 3319,
                        'Date' => '10/21/2013',
                        'Time' => '2:13 PM',
                        'Amount' => '$943.45',
                    ],
                ],
            ]
        ];

        $this->setData($data);
    }
}
