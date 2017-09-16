<?php

namespace Framework\Module\Cngo\Admin\View\Layout;

use Framework\ViewModel\PageLayout;
use Framework\Module\Cngo\Admin\View\ViewModel\NavbarViewModel;
use Framework\ObjectManager\ObjectManager;
use Framework\Controller\ControllerInterface;

class AdminPageLayout extends PageLayout
{
    protected $config = [
        'container' => [
            'Header' => [
                [
                    'viewModel' => NavbarViewModel::class,
                ],
            ],
            'Main' => [],
            'Footer' => [

            ]
        ]
    ];

    protected $template = '/template/layout/admin.phtml';

    protected $asset = '/asset';

    protected $styles = [
        "/material/bootstrap/dist/css/bootstrap.min.css",
        "/material/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css",
        "/material/plugins/bower_components/toast-master/css/jquery.toast.css",
        "/material/plugins/bower_components/morrisjs/morris.css",
        "/material/plugins/bower_components/chartist-js/dist/chartist.min.css",
        "/material/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css",
        "/material/plugins/bower_components/calendar/dist/fullcalendar.css",
        "/material/css/animate.css",
        "/material/css/style.css",
        "/material/css/colors/default.css",
        "/css/app.css",
    ];

    protected $scripts = [
        "/material/plugins/bower_components/jquery/dist/jquery.min.js",
        "/material/bootstrap/dist/js/bootstrap.min.js",
        "/material/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js",
        "/material/js/jquery.slimscroll.js",
        "/material/js/waves.js",
        "/material/plugins/bower_components/waypoints/lib/jquery.waypoints.js",
        "/material/plugins/bower_components/counterup/jquery.counterup.min.js",
        "/material/plugins/bower_components/raphael/raphael-min.js",
        "/material/plugins/bower_components/chartist-js/dist/chartist.min.js",
        "/material/plugins/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js",
        "/material/plugins/bower_components/moment/moment.js",
        "/material/plugins/bower_components/calendar/dist/fullcalendar.min.js",
        "/material/plugins/bower_components/calendar/dist/cal-init.js",
        "/material/js/custom.min.js",
        "/material/js/cbpFWTabs.js",
        "/material/plugins/bower_components/toast-master/js/jquery.toast.js",
        "/material/plugins/bower_components/styleswitcher/jQuery.style.switcher.js",
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
