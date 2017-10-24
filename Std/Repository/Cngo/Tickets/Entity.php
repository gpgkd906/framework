<?php
declare(strict_types=1);

namespace Std\Repository\Tickets;

use Std\Repository\Repository\AbstractEntity;

/**
 * @ORM\Entity(modelClass="Std\Repository\Tickets\Repository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="m_ticket")
 */
class Entity extends AbstractEntity
{
    /**
     * 
     * @ORM\Id
     * @ORM\Column(name="m_ticket_id",type="bigint");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $ticketId = null;

    /**
     * @ORM\Column(name="m_user_id",type="string");
     */
    protected $userId = null;

    /**
     * @ORM\Column(name="price",type="string");
     */
    protected $price = null;

    /**
     * @ORM\ManyToOne(targetEntity="Std\Repository\Users\Entity")
     * @ORM\JoinColumn(name="m_user_id", referencedColumnName="m_user_id")
     */
    protected $user = null;

    
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
     * @param mixed $ticketId
     * @return mixed $ticketId
     * @link
     */
    public function setTicketId ($ticketId)
    {
        return $this->ticketId = $ticketId;
    }

    /**
     * 
     * @api
     * @return mixed $ticketId
     * @link
     */
    public function getTicketId ()
    {
        return $this->ticketId;
    }

    /**
     * 
     * @api
     * @param mixed $price
     * @return mixed $price
     * @link
     */
    public function setPrice ($price)
    {
        return $this->price = $price;
    }

    /**
     * 
     * @api
     * @return mixed $price
     * @link
     */
    public function getPrice ()
    {
        return $this->price;
    }

    /**
     * 
     * @api
     * @param mixed $user
     * @return mixed $user
     * @link
     */
    public function setUser ($user)
    {
        return $this->user = $user;
    }

    /**
     * 
     * @api
     * @return mixed $user
     * @link
     */
    public function getUser ()
    {
        return $this->user;
    }
}
