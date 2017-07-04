<?php

namespace Framework\ViewModel\Admin\Cms;

use Framework\ViewModel\ViewModel\FormViewModel;
use Framework\ViewModel\Admin\Cms\Component;
use Framework\ViewModel\Layout\AdminPageLayout;
use Form2\Validator;

class PageEditViewModel extends FormViewModel
{
    protected $template = '/template/admin/dashboard.html';
        
    protected $config = [
        'layout' => AdminPageLayout::class,
        'container' => [
            'Head' => [
                [
                    'viewModel' => 'Admin\NavbarViewModel',
                ],
            ],
            'Main' => [
                [
                    'viewModel' => Page\EditFormViewModel::class,
                ]
            ]
        ],
    ];

    protected $data = [
        'title' => 'ページ編集',
    ];
}