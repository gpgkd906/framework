<?php
/**
 * PHP version 7
 * File ListViewModel.php
 *
 * @category ViewModel
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\{Module}\View\ViewModel{Namespace};

use Framework\ViewModel\AbstractViewModel;
use Framework\Module\Cngo\Admin\View\Layout\AdminPageLayout;

/**
 * Class ListViewModel
 *
 * @category ViewModel
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ListViewModel extends AbstractViewModel
{
    protected $template = '/template{namespace}/list.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    public $listeners = [
        self::TRIGGER_BEFORE_RENDER => 'onRender',
    ];

    /**
     * Method GetTemplateDir
     *
     * @return string templateDir
     */
    public function getTemplateDir(): string
    {
        return __DIR__ . '/..{ns}';
    }

    /**
     * Method onRender
     *
     * @return void
     */
    public function onRender(): void
    {
        $data = $this->getData();
        $data['{entity}'] = array_map(function (${Entity}) {
            return ${Entity}->toArray();
        }, $data['{entity}']);
        $this->setData($data);
    }
}
