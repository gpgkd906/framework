<?php
/**
 * PHP version 7
 * File ConsoleHelper.php
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
 * Class ConsoleHelper
 * 
 * @category Helper
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ConsoleHelper implements ConsoleHelperInterface
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
    public function confirm($question, bool $default = null, array $confirmations = null)
    {
        if ($confirmations === null) {
            $confirmations = ['y', 'Y'];
        }
        if ($default === null) {
            $default = true;
        }
        do {
            $input = readline($question . '? ');
            if (!in_array($input, $confirmations)) {
                $input = null;
            } else {
                $input = true;
            }
            if ($input === null && $default !== null) {
                $input = $default;
                break;
            }
        } while ($input === null);
        return $input;
    }

    /**
     * Method ask
     *
     * @param string $question question
     * @param bool   $default  defaut answer
     * 
     * @return string $input input answer
     */
    public function ask($question, string $default = null)
    {
        do {
            $input = readline($question . '? ');
            if (!$input && $default !== null) {
                $input = $default;
                break;
            }
        } while (!$input);
        readline_add_history($input);
        return trim($input);
    }

    /**
     * Method choice
     *
     * @param string $question question
     * @param array  $choices  answers
     * @param bool   $default  defaut answer
     * 
     * @return string $input choiced answer
     */
    public function choice($question, array $choices, $default = null)
    {
        $question = $question . '?' . '[' . join('/', $choices) . ']';
        do {
            $input = readline($question . '? ');
            if (!in_array($input, $choices)) {
                $input = null;
            }
            if (!$input && $default !== null && isset($choices[$default])) {
                $input = $choices[$default];
                break;
            }
        } while (!$input);
        readline_add_history($input);
        return trim($input);
    }
}
