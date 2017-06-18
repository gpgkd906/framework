<?php

namespace Framework\Module\Cngo\Front\View\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class ContentViewModel extends AbstractViewModel
{

    protected $template = "/template/content.phtml";

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
