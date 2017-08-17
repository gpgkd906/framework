<?php
/**
 * PHP version 7
 * File ValidatorManagerInterface.php
 * 
 * @category Module
 * @package  Framework\ValidatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ValidatorManager;

/**
 * Interface ValidatorManagerInterface
 * 
 * @category Interface
 * @package  Framework\ValidatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ValidatorManagerInterface
{
    /**
     * Method createInputFilter
     *
     * @param array $inputFilter InputFilterConfig
     * 
     * @return InputFilter $InputFilter
     */
    public function createInputFilter($inputFilter);

    /**
     * Method createValidator
     *
     * @param array $validators ValidatorConfig
     * 
     * @return Validator $validator
     */
    public function createValidator($validators);
}
