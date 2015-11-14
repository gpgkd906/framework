<?php

namespace Framework\Repository\Users;

use Framework\Repository\Repository\AbstractEntity;

/**
 * @ORM\Entity(modelClass="Framework\Repository\Users\Repository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="m_users")
 */
class Entity extends AbstractEntity
{
    /**
     * 
     * @ORM\Id
     * @ORM\Column(name="m_user_id",type="bigint");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $userId = null;

    /**
     * @ORM\Column(name="email",type="string");
     */
    protected $email = null;

    /**
     * @ORM\Column(name="password",type="string");
     */
    protected $password = null;

    /**
     * @ORM\Column(name="register_date",type="date",nullable=true);
     */
    protected $registerDate = null;

    /**
     * @ORM\Column(name="register_time",type="time");
     */
    protected $registerTime = null;

    /**
     * @ORM\Column(name="update_date",type="date");
     */
    protected $updateDate = null;

    /**
     * @ORM\Column(name="update_time",type="time");
     */
    protected $updateTime = null;

    /**
     * @ORM\Column(name="delete_flag",type="integer");
     */
    protected $deleteFlag = null;
    
    /**
     * @ORM\OneToMany(targetEntity="Framework\Repository\Tickets\Entity", fetch=LAZY)
     * @ORM\JoinColumn(name="m_user_id", referencedColumnName="m_user_id")
     */
    protected $ticket = null;

    
    /**
     * 
     * @api
     * @param mixed $userId
     * @return mixed $userId
     * @link
     */
    public function setUserId ($userId)
    {
        return $this->userId = $userId;
    }

    /**
     * 
     * @api
     * @return mixed $userId
     * @link
     */
    public function getUserId ()
    {
        return $this->userId;
    }

    /**
     * 
     * @api
     * @param mixed $email
     * @return mixed $email
     * @link
     */
    public function setEmail ($email)
    {
        return $this->email = $email;
    }

    /**
     * 
     * @api
     * @return mixed $email
     * @link
     */
    public function getEmail ()
    {
        return $this->email;
    }

    /**
     * 
     * @api
     * @param mixed $password
     * @return mixed $password
     * @link
     */
    public function setPassword ($password)
    {
        return $this->password = $password;
    }

    /**
     * 
     * @api
     * @return mixed $password
     * @link
     */
    public function getPassword ()
    {
        return $this->password;
    }

    /**
     * 
     * @api
     * @param mixed $registerDate
     * @return mixed $registerDate
     * @link
     */
    public function setRegisterDate ($registerDate)
    {
        return $this->registerDate = $registerDate;
    }

    /**
     * 
     * @api
     * @return mixed $registerDate
     * @link
     */
    public function getRegisterDate ()
    {
        return $this->registerDate;
    }

    /**
     * 
     * @api
     * @param mixed $registerTime
     * @return mixed $registerTime
     * @link
     */
    public function setRegisterTime ($registerTime)
    {
        return $this->registerTime = $registerTime;
    }

    /**
     * 
     * @api
     * @return mixed $registerTime
     * @link
     */
    public function getRegisterTime ()
    {
        return $this->registerTime;
    }

    /**
     * 
     * @api
     * @param mixed $updateDate
     * @return mixed $updateDate
     * @link
     */
    public function setUpdateDate ($updateDate)
    {
        return $this->updateDate = $updateDate;
    }

    /**
     * 
     * @api
     * @return mixed $updateDate
     * @link
     */
    public function getUpdateDate ()
    {
        return $this->updateDate;
    }

    /**
     * 
     * @api
     * @param mixed $updateTime
     * @return mixed $updateTime
     * @link
     */
    public function setUpdateTime ($updateTime)
    {
        return $this->updateTime = $updateTime;
    }

    /**
     * 
     * @api
     * @return mixed $updateTime
     * @link
     */
    public function getUpdateTime ()
    {
        return $this->updateTime;
    }

    /**
     * 
     * @api
     * @param mixed $deleteFlag
     * @return mixed $deleteFlag
     * @link
     */
    public function setDeleteFlag ($deleteFlag)
    {
        return $this->deleteFlag = $deleteFlag;
    }

    /**
     * 
     * @api
     * @return mixed $deleteFlag
     * @link
     */
    public function getDeleteFlag ()
    {
        return $this->deleteFlag;
    }

    /**
     * 
     * @api
     * @param mixed $ticket
     * @return mixed $ticket
     * @link
     */
    public function setTicket ($ticket)
    {
        return $this->ticket = $ticket;
    }

    /**
     * 
     * @api
     * @return mixed $ticket
     * @link
     */
    public function getTicket ()
    {
        return $this->ticket;
    }
}
