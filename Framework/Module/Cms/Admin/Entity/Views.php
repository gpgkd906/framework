<?php

namespace Framework\Module\Cms\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Views
 *
 * @ORM\Table(name="views", indexes={@ORM\Index(name="fk_page_id_views", columns={"page_id"}), @ORM\Index(name="fk_layout_id_views", columns={"layout_id"})})
 * @ORM\Entity
 */
class Views
{
    /**
     * @var integer
     *
     * @ORM\Column(name="view_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $viewId;

    /**
     * @var string
     *
     * @ORM\Column(name="namespace", type="string", length=32, nullable=true)
     */
    private $namespace;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=64, nullable=true)
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=64, nullable=true)
     */
    private $template;

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
     * @var \Layouts
     *
     * @ORM\ManyToOne(targetEntity="Layouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="layout_id", referencedColumnName="layout_id")
     * })
     */
    private $layout;

    /**
     * @var \Pages
     *
     * @ORM\ManyToOne(targetEntity="Pages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="page_id", referencedColumnName="page_id")
     * })
     */
    private $page;


    /**
     * Get viewId
     *
     * @return integer
     */
    public function getViewId()
    {
        return $this->viewId;
    }

    /**
     * Set namespace
     *
     * @param string $namespace
     *
     * @return Views
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
     * @return Views
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
     * @return Views
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
     * Set createDatetime
     *
     * @param \DateTime $createDatetime
     *
     * @return Views
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
     * @return Views
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
     * @return Views
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
     * Set layout
     *
     * @param \Layouts $layout
     *
     * @return Views
     */
    public function setLayout(\Layouts $layout = null)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get layout
     *
     * @return \Layouts
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set page
     *
     * @param \Pages $page
     *
     * @return Views
     */
    public function setPage(\Pages $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Pages
     */
    public function getPage()
    {
        return $this->page;
    }
}

