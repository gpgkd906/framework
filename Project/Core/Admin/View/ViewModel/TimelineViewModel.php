<?php
declare(strict_types=1);

namespace Project\Core\Admin\View\ViewModel;

use Std\ViewModel\AbstractViewModel;

class TimelineViewModel extends AbstractViewModel
{
    protected $template = '/template/timeline.phtml';

    public $listeners = [
        self::TRIGGER_BEFORE_RENDER => 'onRender',
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }

    public function onRender()
    {
        $data = [
            'timeline' => [
                [
                    'icon' => 'fa-check',
                    'datetime' => '11 hours ago via Twitter',
                    'title' => 'chen han',
                    'body' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero laboriosam dolor perspiciatis omnis exercitationem. Beatae, officia pariatur? Est cum veniam excepturi. Maiores praesentium, porro voluptas suscipit facere rem dicta, debitis.</p>',
                    'action' => null,
                ],
                [
                    'icon' => 'fa-credit-card',
                    'datetime' => null,
                    'title' => 'chen han',
                    'body' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero laboriosam dolor perspiciatis omnis exercitationem. Beatae, officia pariatur? Est cum veniam excepturi. Maiores praesentium, porro voluptas suscipit facere rem dicta, debitis.</p><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero laboriosam dolor perspiciatis omnis exercitationem. Beatae, officia pariatur? Est cum veniam excepturi. Maiores praesentium, porro voluptas suscipit facere rem dicta, debitis.</p>',
                    'action' => null,
                ],
                [
                    'icon' => 'fa-bomb',
                    'datetime' => null,
                    'title' => 'chen han',
                    'body' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero laboriosam dolor perspiciatis omnis exercitationem. Beatae, officia pariatur? Est cum veniam excepturi. Maiores praesentium, porro voluptas suscipit facere rem dicta, debitis.</p>',
                    'action' => null,
                ],
                [
                    'icon' => null,
                    'datetime' => null,
                    'title' => 'chen han',
                    'body' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero laboriosam dolor perspiciatis omnis exercitationem. Beatae, officia pariatur? Est cum veniam excepturi. Maiores praesentium, porro voluptas suscipit facere rem dicta, debitis.</p>',
                    'action' => null,
                ],
                [
                    'icon' => 'fa-save',
                    'datetime' => null,
                    'title' => 'chen',
                    'body' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero laboriosam dolor perspiciatis omnis exercitationem. Beatae, officia pariatur? Est cum veniam excepturi. Maiores praesentium, porro voluptas suscipit facere rem dicta, debitis.</p>',
                    'action' => [
                        [
                            ['title' => 'Action', 'href' => '#'],
                            ['title' => 'Another Action', 'href' => '#'],
                            ['title' => 'Something else here', 'href' => '#'],
                        ],
                        [
                            ['title' => 'Separated link', 'href' => '#'],
                        ]
                    ],
                ],
            ]
        ];
        $this->setData($data);
    }
}
