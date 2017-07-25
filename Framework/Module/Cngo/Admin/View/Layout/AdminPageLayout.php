<?php

namespace Framework\Module\Cngo\Admin\View\Layout;

use Framework\ViewModel\PageLayout;
use Framework\Module\Cngo\Admin\View\ViewModel\NavbarViewModel;

class AdminPageLayout extends PageLayout
{
    protected $config = [
        'container' => [
            'Head' => [
                [
                    'viewModel' => NavbarViewModel::class,
                ],
            ],
            'Main' => [],
        ]
    ];

    protected $template = '/template/layout/admin.phtml';

    protected $asset = '/asset';

    protected $styles = [
        "/sbadmin2/bower_components/bootstrap/dist/css/bootstrap.min.css",
        "/sbadmin2/bower_components/metisMenu/dist/metisMenu.min.css",
        "/sbadmin2/dist/css/timeline.css",
        "/sbadmin2/bower_components/morrisjs/morris.css",
        "/sbadmin2/bower_components/font-awesome/css/font-awesome.min.css",
        "/sbadmin2/dist/css/admin.css",
        "/css/app.css",
    ];

    protected $scripts = [
        "/sbadmin2/bower_components/bootstrap/dist/js/bootstrap.min.js",
        "/sbadmin2/bower_components/metisMenu/dist/metisMenu.min.js",
        "/sbadmin2/bower_components/raphael/raphael-min.js",
        "/sbadmin2/bower_components/morrisjs/morris.min.js",
        "/sbadmin2/dist/js/admin.js",
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
