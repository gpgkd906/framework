<?php

namespace Framework\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;

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