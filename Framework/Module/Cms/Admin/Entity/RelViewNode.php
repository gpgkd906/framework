<?php

namespace Framework\Module\Cms\Admin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RelViewNode
 *
 * @ORM\Table(name="rel_view_node", indexes={@ORM\Index(name="fk_view_id_viewnode", columns={"view_id"}), @ORM\Index(name="fk_node_id_viewnode", columns={"node_id"})})
 * @ORM\Entity
 */
class RelViewNode
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rel_view_node_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $relViewNodeId;

    /**
     * @var \Nodes
     *
     * @ORM\ManyToOne(targetEntity="Nodes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="node_id", referencedColumnName="node_id")
     * })
     */
    private $node;

    /**
     * @var \Views
     *
     * @ORM\ManyToOne(targetEntity="Views")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="view_id", referencedColumnName="view_id")
     * })
     */
    private $view;


    /**
     * Get relViewNodeId
     *
     * @return integer
     */
    public function getRelViewNodeId()
    {
        return $this->relViewNodeId;
    }

    /**
     * Set node
     *
     * @param \Nodes $node
     *
     * @return RelViewNode
     */
    public function setNode(\Nodes $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \Nodes
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set view
     *
     * @param \Views $view
     *
     * @return RelViewNode
     */
    public function setView(\Views $view = null)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return \Views
     */
    public function getView()
    {
        return $this->view;
    }
}

