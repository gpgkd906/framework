<?php
/**
 * PHP version 7
 * File LoginViewModel.php
 * 
 * @category ViewModel
 * @package  Framework\Module\Cngo\AdminUser
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\View\ViewModel;

use Framework\ViewModel\FormViewModel;
use Framework\Module\Cngo\AdminUser\View\Layout\AdminLoginPageLayout;

/**
 * Class LoginViewModel
 * 
 * @category LoginViewModel
 * @package  Framework\Module\Cngo\AdminUser
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class LoginViewModel extends FormViewModel
{
    protected $template = '/template/login.phtml';

    protected $useConfirm = false;

    protected $config = [
        'layout' => AdminLoginPageLayout::class,
        'script' => [
            '/place.js'
        ]
    ];

    protected $fieldset = [
        'adminLogin' => [
            'login' => [
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
                    'placeholder' => 'Login',
                ],
            ],
            'password' => [
                'type' => 'password',
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
                    'placeholder' => 'Password',
                ]
            ],
            'remeber' => [
                'type' => 'checkbox',
                'value' => ['Remeber Me' => 0],
                'attrs' => [
                ],
            ],
        ]
    ];

    /**
     * Method GetTemplateDir
     *
     * @return string templateDir
     */
    public function getTemplateDir()
    {
        return __DIR__ . '/..';
    }
}
