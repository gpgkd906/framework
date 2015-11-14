<?php

namespace Framework\ViewModel\Admin\Cms;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Admin\AdminPageLayout;
use Form2\Validator;

class CmsViewModel extends AbstractViewModel
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
        ],
    ];

    protected $data = [
        'title' => 'Cms',
    ];

    public $listeners = [
        'Render' => 'onRender',
    ];

    public function onRender()
    {
        $this->getLayout()->setPageVar('title', $this->getData()['title']);
    }
}