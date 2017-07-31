<?php

namespace Framework\Module\Cms\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Framework\Repository\Doctrine\AbstractEntity;

/**
 * Controllers
 *
 * @ORM\Table(name="controllers", indexes={@ORM\Index(name="k_controller_c", columns={"class", "delete_flag"}), @ORM\Index(name="fk_controller_group_c", columns={"controller_group_id"})})
 * @ORM\Entity
 */
class Controller extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="controller_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $controllerId;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=256, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    private $priority = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="menu_flag", type="boolean", nullable=false)
     */
    private $menuFlag = '0';

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

    /**
     * @var \ControllerGroup
     *
     * @ORM\ManyToOne(targetEntity="ControllerGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="controller_group_id", referencedColumnName="controller_group_id")
     * })
     */
    private $controllerGroup;


    /**
     * Get controllerId
     *
     * @return integer
     */
    public function getControllerId()
    {
        return $this->controllerId;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return Controllers
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Controllers
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return Controllers
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set menuFlag
     *
     * @param boolean $menuFlag
     *
     * @return Controllers
     */
    public function setMenuFlag($menuFlag)
    {
        $this->menuFlag = $menuFlag;

        return $this;
    }

    /**
     * Get menuFlag
     *
     * @return boolean
     */
    public function getMenuFlag()
    {
        return $this->menuFlag;
    }

    /**
     * Set createDatetime
     *
     * @param \DateTime $createDatetime
     *
     * @return Controllers
     */
    public function setCreateDatetime($createDatetime)
    {
        $this->createDatetime = $createDatetime;

        return $this;
    }

    /**
     * Get createDatetime
     *
     * @return \DateTime
     */
    public function getCreateDatetime()
    {
        return $this->createDatetime;
    }

    /**
     * Set updateDatetime
     *
     * @param \DateTime $updateDatetime
     *
     * @return Controllers
     */
    public function setUpdateDatetime($updateDatetime)
    {
        $this->updateDatetime = $updateDatetime;

        return $this;
    }

    /**
     * Get updateDatetime
     *
     * @return \DateTime
     */
    public function getUpdateDatetime()
    {
        return $this->updateDatetime;
    }

    /**
     * Set deleteFlag
     *
     * @param boolean $deleteFlag
     *
     * @return Controllers
     */
    public function setDeleteFlag($deleteFlag)
    {
        $this->deleteFlag = $deleteFlag;

        return $this;
    }

    /**
     * Get deleteFlag
     *
     * @return boolean
     */
    public function getDeleteFlag()
    {
        return $this->deleteFlag;
    }

    /**
     * Set controllerGroup
     *
     * @param \ControllerGroups $controllerGroup
     *
     * @return Controllers
     */
    public function setControllerGroup(\ControllerGroups $controllerGroup = null)
    {
        $this->controllerGroup = $controllerGroup;

        return $this;
    }

    /**
     * Get controllerGroup
     *
     * @return \ControllerGroups
     */
    public function getControllerGroup()
    {
        return $this->controllerGroup;
    }
}
