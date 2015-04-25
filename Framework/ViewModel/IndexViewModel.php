<?php

namespace Framework\ViewModel;

use Framework\Core\ViewModel\AbstractViewModel;

class IndexViewModel extends AbstractViewModel
{
    
    protected $items = [
        [
            'viewModel' => "HeaderViewModel",
        ],
        [
            'viewModel' => "ContentViewModel",
        ],
        [
            'viewModel' => "FooterViewModel"
        ]
    ];
}