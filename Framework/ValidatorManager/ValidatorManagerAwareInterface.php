<?php
/**
 * PHP version 7
 * File ValidatorManagerAwareInterface.php
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
 * Interface ValidatorManagerAwareInterface
 * 
 * @category Interface
 * @package  Framework\ValidatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ValidatorManagerAwareInterface
{
    /**
     * Method setValidatorManager
     *
     * @param ValidatorManagerInterface $ValidatorManager ValidatorManager
     * 
     * @return mixed
     */
    public function setValidatorManager(ValidatorManagerInterface $ValidatorManager);

    /**
     * Method getValidatorManager
     *
     * @return ValidatorManagerInterface $ValidatorManager
     */
    public function getValidatorManager();
}
