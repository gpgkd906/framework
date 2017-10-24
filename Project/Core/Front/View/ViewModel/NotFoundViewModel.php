<?php
declare(strict_types=1);

namespace Project\Core\Front\View\ViewModel;

use Std\ViewModel\AbstractViewModel;
use Project\Core\Front\View\Layout\FrontPageLayout;

class NotFoundViewModel extends AbstractViewModel
{
    protected $template = '/template/error/404.phtml';

    protected $config = [
        'layout' => FrontPageLayout::class,
    ];

    public function getTemplateDir(): string
    {
        return __DIR__ . '/..';
    }
}
