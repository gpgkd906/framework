<?php
declare(strict_types=1);

namespace Project\Core\Front\View\ViewModel;

use Std\ViewModel\AbstractViewModel;
use Project\Core\Front\View\Layout\FrontPageLayout;

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

    public function getTemplateDir(): string
    {
        return __DIR__ . '/..';
    }
}
