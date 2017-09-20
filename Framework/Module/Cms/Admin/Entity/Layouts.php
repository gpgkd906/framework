<?php

namespace Framework\Module\Cms\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Layouts
 *
 * @ORM\Table(name="layouts")
 * @ORM\Entity
 */
class Layouts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="layout_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $layoutId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="namespace", type="string", length=64, nullable=true)
     */
    private $namespace;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=32, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=128, nullable=true)
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(name="sample", type="string", length=128, nullable=true)
     */
    private $sample;

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
     * Get layoutId
     *
     * @return integer
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Layouts
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set namespace
     *
     * @param string $namespace
     *
     * @return Layouts
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Get namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return Layouts
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
     * Set template
     *
     * @param string $template
     *
     * @return Layouts
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set sample
     *
     * @param string $sample
     *
     * @return Layouts
     */
    public function setSample($sample)
    {
        $this->sample = $sample;

        return $this;
    }

    /**
     * Get sample
     *
     * @return string
     */
    public function getSample()
    {
        return $this->sample;
    }

    /**
     * Set createDatetime
     *
     * @param \DateTime $createDatetime
     *
     * @return Layouts
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
     * @return Layouts
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
     * @return Layouts
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
}

