<?php
/**
 * PHP version 7
 * File EditViewModel.php
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
 * Class EditViewModel
 * 
 * @category ViewModel
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class EditViewModel extends RegisterViewModel
{
    protected $template = '/template{namespace}/edit.phtml';

    public $listeners = [
        'Render' => 'onRender',
    ];
    
    /**
     * Method GetTemplateDir
     *
     * @return string templateDir
     */
    public function getTemplateDir()
    {
        return __DIR__ . '/..{ns}';
    }

    /**
     * Method onRender
     *
     * @return void
     */
    public function onRender()
    {
        $data = $this->getData();
        $data['{entity}'] = $data['{entity}']->toArray();
        $formData = $this->getForm()->getData();
        if (isset($formData['{entity}'])) {
            $data['{entity}'] = array_merge($data['{entity}'], $formData['{entity}']);
        }
        $this->getForm()->setData($data);
    }
}
