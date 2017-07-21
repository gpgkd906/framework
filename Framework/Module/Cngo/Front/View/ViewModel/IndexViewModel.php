<?php

namespace Framework\Module\Cngo\Front\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;

class IndexViewModel extends AbstractViewModel
{

    protected $config = [
        'container' => [
            'main' => [
                [ 'viewModel' => HeaderViewModel::class, ],
                [ 'viewModel' => ContentViewModel::class, ],
                [ 'viewModel' => FooterViewModel::class ],
            ],
        ],
    ];
}
