<?php

namespace Framework\Module\Cngo\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Framework\Repository\Doctrine\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="Framework\Module\Cngo\Admin\Entity\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="m_users")
 */
class User extends AbstractEntity
{
    /**
     *
     * @ORM\Id
     * @ORM\Column(name="m_user_id",type="bigint");
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $userId = null;

    /**
     * @ORM\Column(name="price",type="string");
     */
    protected $price = null;

    // /**
    //  * @ORM\ManyToOne(targetEntity="Framework\Repository\Users\Entity")
    //  * @ORM\JoinColumn(name="m_user_id", referencedColumnName="m_user_id")
    //  */
    // protected $user = null;
    //

    /**
     *
     * @api
     * @param mixed $userId
     * @return mixed $userId
     * @link
     */
    public function setUserId($userId)
    {
        return $this->userId = $userId;
    }

    /**
     *
     * @api
     * @return mixed $userId
     * @link
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     *
     * @api
     * @param mixed $price
     * @return mixed $price
     * @link
     */
    public function setPrice($price)
    {
        return $this->price = $price;
    }

    /**
     *
     * @api
     * @return mixed $price
     * @link
     */
    public function getPrice()
    {
        return $this->price;
    }
}
