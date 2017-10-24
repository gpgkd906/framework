<?php
declare(strict_types=1);

namespace Project\Core\Front\View\ViewModel;

use Std\ViewModel\AbstractViewModel;

class FooterViewModel extends AbstractViewModel
{

    protected $template = "/template/footer.phtml";

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
