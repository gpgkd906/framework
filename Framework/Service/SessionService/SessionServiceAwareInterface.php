<?php
/**
 * PHP version 7
 * File SessionServiceAwareInterface.php
 * 
 * @category Service
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Service\SessionService;

/**
 * Interface SessionServiceAwareInterface
 * 
 * @category Interface
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface SessionServiceAwareInterface
{
    /**
     * Method setSessionService
     *
     * @param SessionService $SessionService SessionService
     * 
     * @return mixed
     */
    public function setSessionService(SessionService $SessionService);

    /**
     * Method getSessionService
     *
     * @return SessionService $SessionService
     */
    public function getSessionService();
}
