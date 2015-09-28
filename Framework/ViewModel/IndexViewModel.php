<?php

namespace Framework\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class IndexViewModel extends AbstractViewModel
{
    
    protected $config = [
        'container' => [
            'main' => [
                [ 'viewModel' => "HeaderViewModel", ],
                [ 'viewModel' => "ContentViewModel", ],
                [ 'viewModel' => "FooterViewModel" ],
            ],
        ],
    ];
}