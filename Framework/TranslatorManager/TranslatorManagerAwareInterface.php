<?php
/**
 * PHP version 7
 * File TranslatorManagerAwareInterface.php
 * 
 * @category Module
 * @package  Framework\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\TranslatorManager;

/**
 * Class TranslatorManagerAwareInterface
 * 
 * @category Class
 * @package  Framework\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface TranslatorManagerAwareInterface
{
    /**
     * Method setTranslatorManager
     *
     * @param TranslatorManagerInterface $TranslatorManager Object
     * 
     * @return mixed
     */
    public function setTranslatorManager(TranslatorManagerInterface $TranslatorManager);

    /**
     * Method getTranslatorManager
     *
     * @return TranslatorManagerInterface $TranslatorManager
     */
    public function getTranslatorManager();
}
