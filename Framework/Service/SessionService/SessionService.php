<?php

namespace Framework\Service\SessionService;

use Framework\Service\AbstractService;
use Exception;

class SessionService extends AbstractService
{
    /**
     *
     * @api
     * @var mixed $session
     * @access private
     * @link
     */
    private $session = null;

    private $closeFlag = false;

    /**
     *
     * @api
     * @param mixed $session
     * @return mixed $session
     * @link
     */
    public function setSession ($session)
    {
        return $this->session = $session;
    }

    /**
     *
     * @api
     * @return mixed $session
     * @link
     */
    public function getSession ()
    {
        return $this->session;
    }

    public function getSection($name)
    {
        if (isset($this->session[$name])) {
            return $this->session[$name];
        }
    }

    public function setSection($name, $section)
    {
        if ($this->closeFlag) {
            throw new Exception('Cannot send session cookie - headers already send');
        }
        $this->session[$name] = $section;
    }

    public function __construct()
    {
        $this->reload();
    }

    public function __destruct()
    {
        if (session_status() !== PHP_SESSION_NONE) {
            $this->write();
        }
    }

    public function reload()
    {
        if ($this->closeFlag === false) {
            session_start();
            $this->setSession($_SESSION);
            session_abort();
        }
    }

    public function write()
    {
        if ($this->closeFlag === false) {
            session_start();
            $_SESSION = array_merge($_SESSION, $this->getSession());
            session_write_close();
        }
    }

    public function close()
    {
        $this->write();
        $this->closeFlag = true;
    }
}
