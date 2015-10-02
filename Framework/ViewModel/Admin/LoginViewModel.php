<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\FormViewModel;

class LoginViewModel extends FormViewModel
{
    protected $template = '/template/admin/login.html';
    
    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $data = [
        'title' => 'ログイン',
    ];

    public $listeners = [
        'Render' => 'onRender',
    ];

    public function onRender()
    {
        $this->getLayout()->setPageVar('title', $this->getData()['title']);
    }
}