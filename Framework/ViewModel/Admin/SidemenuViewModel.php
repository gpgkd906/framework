<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use NumberFormatter;

class SidemenuViewModel extends AbstractViewModel
{
    const TRIGGER_MENUINIT = 'menu_init';
    
    protected $template = '/template/admin/component/sidemenu.html';
    
    protected $data = [
        [
            'title' => 'Dashboard',
            'link' => '/admin/dashboard',
            'icon' => [
                'class' => 'fa fa-dashboard fa-fw',
            ],
        ],
        [
            'title' => '会員管理',
            'icon'  => 'fa-users',
            'child' => [
                [
                    'title' => '会員一覧',
                    'link'  => '/admin/customer/',
                ],
                [
                    'title' => 'ダミ',
                    'link'  => '#',
                ],
            ],
        ],
        [
            'title' => 'チケット管理',
            'icon'  => 'fa-bar-chart-o',
            'child' => [
                [
                    'title' => 'チケット一覧',
                    'link'  => '/admin/ticket/',
                ],
                [
                    'title' => 'ダミ',
                    'link'  => '#',
                ],
            ],
        ],
        [
            'title' => 'タスク管理',
            'icon'  => 'fa-bar-chart-o',
            'child' => [
                [
                    'title' => 'タスク一覧',
                    'link'  => '/admin/task/',
                ],
                [
                    'title' => 'ダミ',
                    'link'  => '#',
                ],
            ],
        ],
        [
            'title' => 'コンテンツ管理',
            'icon'  => 'fa-wrench',
            'child' => [
                [
                    'title' => 'ページ管理',
                    'link'  => '/admin/cms/page/',
                ],
                [
                    'title' => 'ビュー管理',
                    'link'  => '/admin/cms/view/',
                ],
            ],
        ],
        [
            'title' => 'プラグイン管理',
            'icon'  => 'fa-wrench',
            'child' => [
                [
                    'title' => 'プラグイン一覧',
                    'link'  => '/admin/plugin/',
                ],
                [
                    'title' => 'ダミ',
                    'link'  => '#',
                ],
            ],
        ],
        [
            'title' => '設定',
            'icon'  => 'fa-sitemap',
            'child' => [
                [
                    'title' => '基本設定',
                    'child' => [
                        [
                            'title' => 'グローバル設定',
                            'link'  => '/admin/setting/base/global/',
                        ],
                        [
                            'title' => '基本設定2',
                            'link'  => '/admin/setting/base/',
                        ],
                        [
                            'title' => '基本設定3',
                            'link'  => '/admin/setting/base/',
                        ],
                        [
                            'title' => '基本設定4',
                            'link'  => '/admin/setting/base/',
                        ],
                    ]
                ],
                [
                    'title' => 'システム設定',
                    'child' => [
                        [
                            'title' => 'データソース設定',
                            'link'  => '/admin/setting/system/model/',
                        ],
                        [
                            'title' => 'システム設定2',
                            'link'  => '/admin/setting/system/',
                        ],                        
                        [
                            'title' => 'システム設定3',
                            'link'  => '/admin/setting/system/',
                        ],                        
                        [
                            'title' => 'システム設定4',
                            'link'  => '/admin/setting/system/',
                        ],                        
                    ]
                ],
            ],
        ],
    ];
        
    public function getData($key = null)
    {
        $this->triggerEvent(self::TRIGGER_MENUINIT);
        return parent::getData($key);
    }
}