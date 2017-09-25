<?php

namespace Framework\Module\Cngo\AdminUser\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelRoleResource
 *
 * @ORM\Table(name="rel_role_resource", indexes={@ORM\Index(name="fk_role_id_roleResourcee", columns={"role_id"}), @ORM\Index(name="fk_resource_id_roleResourcee", columns={"resource_id"})})
 * @ORM\Entity
 */
class RelRoleResource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rel_role_resource_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $relRoleResourceId;

    /**
     * @var \Resources
     *
     * @ORM\ManyToOne(targetEntity="Resources")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resource_id", referencedColumnName="resource_id")
     * })
     */
    private $resource;

    /**
     * @var \Roles
     *
     * @ORM\ManyToOne(targetEntity="Roles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="role_id")
     * })
     */
    private $role;


    /**
     * Get relRoleResourceId
     *
     * @return integer
     */
    public function getRelRoleResourceId()
    {
        return $this->relRoleResourceId;
    }

    /**
     * Set resource
     *
     * @param \Resources $resource
     *
     * @return RelRoleResource
     */
    public function setResource(\Resources $resource = null)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource
     *
     * @return \Resources
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set role
     *
     * @param \Roles $role
     *
     * @return RelRoleResource
     */
    public function setRole(\Roles $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Roles
     */
    public function getRole()
    {
        return $this->role;
    }
}

