<?php

namespace Framework\ViewModel\Front;

use Framework\ViewModel\ViewModel\AbstractViewModel;

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