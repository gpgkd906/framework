<?php
/**
 * PHP version 7
 * File ConsoleHelperInterface.php
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
 * Interface ConsoleHelperInterface
 * 
 * @category Helper
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ConsoleHelperInterface
{
    /**
     * Method confirm
     *
     * @param string $question      question
     * @param bool   $default       defaut answer
     * @param array  $confirmations answers
     * 
     * @return string $input choiced answer
     */
    public function confirm($question, bool $default = null, array $confirmations = null);

    /**
     * Method ask
     *
     * @param string $question question
     * @param bool   $default  defaut answer
     * 
     * @return string $input input answer
     */
    public function ask($question, string $default = null);

    /**
     * Method choice
     *
     * @param string $question question
     * @param array  $choices  answers
     * @param bool   $default  defaut answer
     * 
     * @return string $input choiced answer
     */
    public function choice($question, array $choices, $default = null);
}
