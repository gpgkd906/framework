<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\Front\View\ViewModel;

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Front\View\Layout\FrontPageLayout;

class IndexViewModel extends AbstractViewModel
{
    protected $template = '/template/index.phtml';

    protected $config = [
        'layout' => FrontPageLayout::class,
        'container' => [
            'Main' => [
                [ 'viewModel' => HeaderViewModel::class, ],
                [ 'viewModel' => ContentViewModel::class, ],
                [ 'viewModel' => FooterViewModel::class ],
            ],
        ],
    ];

    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
