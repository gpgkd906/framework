<?php

namespace Framework\Module\Cngo\Front\View\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class HeaderViewModel extends AbstractViewModel
{

    protected $template = "/template/header.phtml";

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
