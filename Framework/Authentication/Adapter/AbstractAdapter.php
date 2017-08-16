<?php
/**
 * PHP version 7
 * File AbstractAdapter.php
 * 
 * @category Authentication
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Authentication\Adapter;

use Zend\Authentication\Adapter\AdapterInterface;
use Framework\Authentication\AbstractAuthentication;

/**
 * Class AbstractAdapter
 * 
 * @category Class
 * @package  Framework\Authentication
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractAdapter implements AdapterInterface
{
    protected $username;
    protected $password;

    /**
     * Method __construct
     *
     * @param string|null $username UserName
     * @param string|null $password Password
     */
    public function __construct($username = null, $password = null)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Method setUsername
     *
     * @param string $username UserName
     * 
     * @return this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Method getUsername 
     *
     * @return string username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Method setPassword
     *
     * @param string $password Password
     * 
     * @return this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Method getPassword
     *
     * @return string password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Abstract Method authenticate
     *
     * @return bool
     */
    abstract public function authenticate();

    /**
     * Method getCrypt
     *
     * @return Bcrypt
     */
    public function getCrypt()
    {
        return AbstractAuthentication::getCrypt();
    }
}
