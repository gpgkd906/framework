<?php
declare(strict_types=1);

namespace Project\Core\Front\View\ViewModel;

use Std\ViewModel\AbstractViewModel;

class HeaderViewModel extends AbstractViewModel
{

    protected $template = "/template/header.phtml";

    public function getTemplateDir(): string
    {
        return __DIR__ . '/..';
    }
}
