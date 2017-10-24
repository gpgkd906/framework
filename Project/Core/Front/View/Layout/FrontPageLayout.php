<?php

namespace Project\Core\Front\View\Layout;

use Std\ViewModel\PageLayout;
use Project\Core\Admin\View\ViewModel\NavbarViewModel;

class FrontPageLayout extends PageLayout
{
    protected $config = [
        'container' => [
            'Head' => [],
            'Main' => [],
        ]
    ];

    protected $template = '/template/layout/front.phtml';

    protected $asset = '/asset/material';

    protected $styles = [
        '/bootstrap/dist/css/bootstrap.min.css',
        '/css/animate.css',
        '/css/colors/default.css'
    ];

    protected $scripts = [
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
