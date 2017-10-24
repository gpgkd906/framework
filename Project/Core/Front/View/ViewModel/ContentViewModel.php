<?php
declare(strict_types=1);

namespace Project\Core\Front\View\ViewModel;

use Std\ViewModel\AbstractViewModel;

class ContentViewModel extends AbstractViewModel
{

    protected $template = "/template/content.phtml";

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
