<?php

namespace Framework\ViewModel\Admin;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class NavbarViewModel extends AbstractViewModel
{
    protected $template = '/template/admin/component/navbar.html';

    protected $config = [
        'container' => [
            'Side' => [
                [ 'viewModel' => 'Admin\SidemenuViewModel' ],
            ],
        ],
    ];
}