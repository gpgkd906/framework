<?php

namespace Framework\Module\Cngo\AdminTop\View\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class NavbarViewModel extends AbstractViewModel
{
    protected $template = '/template/component/navbar.html';

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }

    protected $config = [
        'container' => [
            'Side' => [
                [ 'viewModel' => SidemenuViewModel::class ],
            ],
        ],
    ];
}
