<?php
/**
 * PHP version 7
 * File ControllerInterface.php
 *
 * @category Controller
 * @package  Framework\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Controller;

/**
 * Interface ControllerInterface
 *
 * @category Interface
 * @package  Framework\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ConsoleInterface extends ControllerInterface
{
    /**
     * Method getDescription
     *
     * @return string $descript PageDescription
     */
    public static function getDescription();

    /**
     * Abstract Method getHelp
     *
     * @return string
     */
    public function getHelp();
}
