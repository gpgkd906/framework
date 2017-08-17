<?php
/**
 * PHP version 7
 * File ValidatorManager.php
 * 
 * @category Module
 * @package  Framework\ValidatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ValidatorManager;

use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;
use Framework\ObjectManager\SingletonInterface;

/**
 * Interface ValidatorManager
 * 
 * @category Interface
 * @package  Framework\ValidatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ValidatorManager implements 
    ValidatorManagerInterface,
    SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    /**
     * Method createInputFilter
     *
     * @param array $inputFilter InputFilterConfig
     * 
     * @return InputFilter $InputFilter
     */
    public function createInputFilter($inputFilter = null)
    {
        $InputFilter = new InputFilter();
        if ($inputFilter) {
            $InputFilter->add($inputFilter);
        }
        return $InputFilter;
    }

    /**
     * Method createValidator
     *
     * @param array $validators ValidatorConfig
     * 
     * @return Validator $validator
     */
    public function createValidator($validators)
    {
        throw new \Exception("Not implements");
    }
}
