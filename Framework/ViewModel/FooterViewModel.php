<?php

namespace Framework\ViewModel;

use Framework\Core\ViewModel\AbstractViewModel;

class FooterViewModel extends AbstractViewModel
{
    
    protected $template = "/template/footer/index.phtml";

    public $listeners = [
        
        "Display" => "onDisplay"
    ];

    public function onDisplay()
    {
        var_dump("footer");
    }
    
}