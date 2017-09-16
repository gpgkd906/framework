<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Front\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Front\View\Layout\FrontPageLayout;

class NotFoundViewModel extends AbstractViewModel
{
    protected $template = '/template/error/404.phtml';

    protected $config = [
        'layout' => FrontPageLayout::class,
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
