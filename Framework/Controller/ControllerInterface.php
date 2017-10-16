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
interface ControllerInterface
{
    /**
     * Method callActionFlow
     *
     * @param string $action action
     * @param string $param  parameter
     *
     * @return viewModel
     */
    public function callActionFlow($action, $param);

    /**
     * Method response
     *
     * @return void
     */
    public function response();

    /**
     * Method getDescription
     *
     * @return string
     */
    public function getDescription();
}
