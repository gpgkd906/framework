<?php

namespace Framework\Service\SessionService;

use Framework\Service\AbstractService;
    
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
        if(isset($this->session[$name])) {
            return $this->session[$name];
        }
    }

    public function setSection($name, $section)
    {
        $this->session[$name] = $section;
    }
    
    public function __construct()
    {
        $this->reload();
    }

    public function __destruct()
    {
        $this->writeAndClose();
    }

    public function reload()
    {
        session_start();
        $this->setSession($_SESSION);
        session_write_close();
    }

    public function writeAndClose()
    {
        session_start();
        $_SESSION = $this->getSession();
        session_write_close();
    }    
}
