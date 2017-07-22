<?php

namespace Framework\Module\Cngo\Admin;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntityAdminUsers
 *
 * @ORM\Table(name="admin_users", uniqueConstraints={@ORM\UniqueConstraint(name="uk_login", columns={"login"})})
 * @ORM\Entity
 */
class EntityAdminUsers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="admin_users_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $adminUsersId;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=32, nullable=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=64, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_datetime", type="datetime", nullable=true)
     */
    private $createDatetime = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_datetime", type="datetime", nullable=true)
     */
    private $updateDatetime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="delete_flag", type="boolean", nullable=false)
     */
    private $deleteFlag = '0';


}

