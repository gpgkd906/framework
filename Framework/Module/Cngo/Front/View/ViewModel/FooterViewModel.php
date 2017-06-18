<?php

namespace Framework\Module\Cngo\Front\View\ViewModel;

use Framework\ViewModel\ViewModel\AbstractViewModel;

class FooterViewModel extends AbstractViewModel
{

    protected $template = "/template/footer.phtml";

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
