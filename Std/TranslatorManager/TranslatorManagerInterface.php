<?php
/**
 * PHP version 7
 * File TranslatorManagerInterface.php
 * 
 * @category Module
 * @package  Std\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\TranslatorManager;

/**
 * Class TranslatorManagerInterface
 * 
 * @category Class
 * @package  Std\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface TranslatorManagerInterface
{
    const VALIDATOR = 'validator';

    /**
     * Method getTranslator
     *
     * @param string $type Type
     * 
     * @return Translator $translator
     */
    public function getTranslator($type);
}
