<?php

namespace Framework\Module\Cngo\Front\View\Layout;

use Framework\ViewModel\PageLayout;
use Framework\Module\Cngo\Admin\View\ViewModel\NavbarViewModel;

class FrontPageLayout extends PageLayout
{
    protected $config = [
        'container' => [
            'Head' => [],
            'Main' => [],
        ]
    ];

    protected $template = '/template/layout/front.phtml';

    protected $asset = '/asset';

    protected $styles = [
    ];

    protected $scripts = [
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
