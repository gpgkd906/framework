<?php
/**
 * PHP version 7
 * File RegisterViewModel.php
 *
 * @category ViewModel
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\{Module}\View\ViewModel{Namespace};

use Std\ViewModel\FormViewModel;
use Project\Core\Admin\View\Layout\AdminPageLayout;

/**
 * Class RegisterViewModel
 *
 * @category ViewModel
 * @package  Framework\Module\{Module}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class RegisterViewModel extends FormViewModel
{
    protected $template = '/template{namespace}/register.phtml';

    protected $config = [
        'layout' => AdminPageLayout::class,
    ];

    protected $fieldset = [
        '{entity}' => [
            'column' => [
                'type' => 'text',
                'inputSpecification' => [
                    'require' => true,
                    'validators' => [
                        [
                            'name' => 'NotEmpty',
                        ],
                    ]
                ],
                'attrs' => [
                    'class' => 'form-control',
                    'placeholder' => 'カラム',
                ],
            ],
        ]
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
}
