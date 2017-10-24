<?php

namespace Project\Core\AdminUser\View\Layout;

use Std\ViewModel\PageLayout;
use Project\Core\AdminUser\View\ViewModel\NavbarViewModel;
use Std\ViewModel\TwigRenderer;
class AdminLoginPageLayout extends PageLayout
{
    protected $config = [
        'container' => [
            'Main' => [],
        ]
    ];

    protected $template = '/template/layout/login.phtml';

    protected $asset = '/asset';

    protected $styles = [
        "/material/bootstrap/dist/css/bootstrap.min.css",
        "/material/css/animate.css",
        "/material/css/style.css",
        "/material/css/colors/default.css"
    ];

    protected $scripts = [
        "/material/plugins/bower_components/jquery/dist/jquery.min.js",
        "/material/bootstrap/dist/js/bootstrap.min.js",
        "/material/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js",
        "/material/js/jquery.slimscroll.js",
        "/material/js/waves.js",
        "/material/js/custom.min.js",
        "/material/plugins/bower_components/styleswitcher/jQuery.style.switcher.js",
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }

    public function getRenderer()
    {
        return $this->getObjectManager()->create(null, TwigRenderer::class);
    }
}
