<?php
/**
 * PHP version 7
 * File AbstractConsole.php
 *
 * @category Controller
 * @package  Framework\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Controller;

use Exception;

/**
 * Class AbstractConsole
 *
 * @category Class
 * @package  Framework\Controller
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractConsole extends AbstractController implements
    ConsoleInterface
{
    /**
     * Method callActionFlow
     *
     * @param string $action action
     * @param string $param  parameter
     *
     * @return void
     */
    public function callActionFlow($action, $param)
    {
        if (is_callable([$this, $action])) {
            $this->triggerEvent(self::TRIGGER_BEFORE_RESPONSE);
            $this->callAction($action, $param);
            $this->triggerEvent(self::TRIGGER_AFTER_RESPONSE);
        } else {
            throw new Exception(sprintf("not found implementions for action[%s]", $action));
        }
    }

    /**
     * Method getDescription
     *
     * @return string $descript PageDescription
     */
    public function getDescription()
    {
        return 'input Class Description';
    }

    /**
     * Abstract Method getHelp
     *
     * @return string
     */
    abstract public function getHelp();

    /**
     * Method getPriority
     *
     * @return integer
     */
    public function getPriority()
    {
        return 99;
    }
}
