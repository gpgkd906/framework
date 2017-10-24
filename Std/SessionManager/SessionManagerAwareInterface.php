<?php
/**
 * PHP version 7
 * File SessionManagerAwareInterface.php
 *
 * @category Service
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\SessionManager;

/**
 * Interface SessionManagerAwareInterface
 *
 * @category Interface
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface SessionManagerAwareInterface
{
    /**
     * Method setSessionManager
     *
     * @param SessionManager $SessionManager SessionManager
     *
     * @return mixed
     */
    public function setSessionManager(SessionManager $SessionManager);

    /**
     * Method getSessionManager
     *
     * @return SessionManager $SessionManager
     */
    public function getSessionManager();
}
