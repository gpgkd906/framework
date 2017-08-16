<?php
/**
 * PHP version 7
 * File ConsoleHelperAwareInterface.php
 * 
 * @category Module
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Helper\Console;

/**
 * Interface ConsoleHelperAwareInterface
 * 
 * @category Helper
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ConsoleHelperAwareInterface
{
    /**
     * Method setConsoleHealper
     *
     * @param ConsoleHelperInterface $ConsoleHelper ConsoleHelper
     * 
     * @return this
     */
    public function setConsoleHelper(ConsoleHelperInterface $ConsoleHelper);

    /**
     * Method getConsoleHelper
     *
     * @return ConsoleHelperInterface
     */
    public function getConsoleHelper();
}
