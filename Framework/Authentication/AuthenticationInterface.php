<?php
/**
 * PHP version 7
 * File AuthenticationInterface.php
 * 
 * @category Authentication
 * @package  Framework\Authentication
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Authentication;

/**
 * Interface AuthenticationInterface
 * 
 * @category Authentication
 * @package  Framework\Authentication
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface AuthenticationInterface
{
    /**
     * Method login
     *
     * @param string $username UserName
     * @param string $password Password
     * 
     * @return void
     */
    public function login($username, $password);
    
    /**
     * Method updateIdentity
     *
     * @param array $Identity Identity
     * 
     * @return void
     */
    public function updateIdentity($Identity);
}
