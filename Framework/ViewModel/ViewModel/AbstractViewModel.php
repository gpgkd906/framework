<?php
namespace Framework\ViewModel\ViewModel;

use Framework\Event\Event\EventInterface;
use Exception;

abstract class AbstractViewModel implements ViewModelInterface, EventInterface
{
    use \Framework\Event\Event\EventTrait;
    
    const renderAsHTML = "html";
    const renderAsJSON = "json";
    const renderAsXML = "xml";

    //error
    const ERROR_INVALID_RENDER_TYPE = "error: invalid render-type";
    const ERROR_INVALID_RENDER_TEMPLATE = "error: invalid render template [%s]";
    
    //trigger
    const TRIGGER_RENDER = "Render";
    const TRIGGER_DISPLAY = "Display";
    
    protected $template = null;
    protected $data = [];
    protected $renderType = "html";
    protected $templateDir = null;
    private $childs = [];
    private $id = null;
    static private $incrementId = 0;
    public $listeners = [];

    /**
     *
     * @api
     * @var mixed $config 
     * @access private
     * @link
     */
    protected $config = [];

    /**
     *
     * @api
     * @var mixed $layout 
     * @access private
     * @link
     */
    private $layout = null;

    /**
     *
     * @api
     * @var mixed $container 
     * @access private
     * @link
     */
    private $containers = null;

    /**
     * 
     * @api
     * @param mixed $config
     * @return mixed $config
     * @link
     */
    public function setConfig ($config)
    {
        return $this->config = $config;
    }

    /**
     * 
     * @api
     * @return mixed $config
     * @link
     */
    public function getConfig ()
    {
        return $this->config;
    }
    
    static private function getIncrementId()
    {
        self::$incrementId ++;
        return "ViewModel_" . self::$incrementId;
    }

    public function __construct($config) {
        $config = array_merge($this->getConfig(), $config);
        $this->setConfig($config);
        if(isset($config["id"])) {
            $this->id = $id;
        } else {
            $this->id = self::getIncrementId();
        }
        //data:template
        if(isset($config["data"])) {
            $this->setData($config["data"]);
        }
        //layout;
        if(isset($config['layout'])) {
            $layoutClass = $config['layout'];
            $this->setLayout($layoutClass::getSingleton());
        } else {
            $this->setLayout(PageLayout::getSingleton());
        }
        //container:template
        if(isset($config['container'])) {
            $containers = [];
            foreach($config['container'] as $containerName => $containerConfig) {
                $containers[$containerName] = new Container($containerConfig, $this);
            }
            $this->setContainers($containers);
        }
        //ViewModel Event init
        foreach($this->listeners as $event => $listener) {
            $this->addEventListener($event, [$this, $listener]);
        }
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function setRenderType($renderType)
    {
        $this->renderType = $renderType;
    }

    public function getRenderType()
    {
        return $this->renderType;
    }

    public function setTemplate($template)
    {        
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;
    }

    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    public function getTemplateForRender()
    {
        $template = $this->getTemplate();
        if($template === null) {
            return null;
        }
        if(is_file($template)) {
            return $template;
        }
        $template = $this->getTemplateDir() . $this->getTemplate();
        if(is_file($template)) {
            return $template;
        }
        throw new Exception(sprintf(self::ERROR_INVALID_RENDER_TEMPLATE, $template));
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getChild($id)
    {
        $childs = $this->getChilds();
        if(isset($childs[$id])) {
            return $childs[$id];
        }
        return null;
    }

    public function setChilds($childs)
    {
        return $this->childs = $childs;
    }

    public function getChilds()
    {
        if(empty($this->childs)) {
            foreach($this->getContainers() as $container) {
                $this->childs += $container->getItems();
            }
        }
        return $this->childs;
    }
    
    public function render($renderType = null)
    {
        $this->triggerEvent(self::TRIGGER_RENDER);
        if($renderType === null) {
            $renderType = $this->renderType;
        }
        switch($renderType) {
        case static::renderAsHTML:
            $display = $this->asHtml();
            break;
        case static::renderAsJSON:
            $display = $this->asJson();
            break;
        case static::renderAsXML:
            $display = $this->asXml();
            break;
        default:
            throw new Exception(self::ERROR_INVALID_RENDER_TYPE);
            break;
        }
        $this->triggerEvent(self::TRIGGER_DISPLAY);
        return $display;
    }

    public function asHtml()
    {
        $htmls = [];
        if($template = $this->getTemplateForRender()) {
            ob_start();
            $data = $this->escapeHtml($this->getData());
            extract($data);
            require $template;
            $htmls[] = ob_get_contents();
            ob_end_clean();
        } else {
            //templateがなければ....
            foreach($this->getChilds() as $child) {
                $htmls[] = $child->render();
            }
        }
        return join("", $htmls);
    }
    
    public function asJson()
    {
        $data = [
            "data" => $this->getData(),
            "childrens" => []
        ];
        foreach($this->getChilds() as $child) {
            $subData = $child->getData();
            if(!empty($subData)) {
                $data["childrens"][] = $subData;
            }
        }
        return json_encode($data);
    }
    
    public function asXml()
    {
        
    }

    public function __toString()
    {
        return $this->render();
    }

    static public function escapeHtml($data)
    {
        if(is_array($data)){
            foreach($data as $key => $value){
                $data[$key] = self::escapeHtml($value);
            }
            return $data;
        } elseif(is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES);
        }else{
            return $data;
        }        
    }

    /**
     * 
     * @api
     * @param mixed $layout
     * @return mixed $layout
     * @link
     */
    public function setLayout (LayoutInterface $layout)
    {
        return $this->layout = $layout;
    }

    /**
     * 
     * @api
     * @return mixed $layout
     * @link
     */
    public function getLayout ()
    {
        return $this->layout;
    }

    /**
     * 
     * @api
     * @param mixed $container
     * @return mixed $container
     * @link
     */
    public function setContainers ($containers)
    {
        foreach($containers as $index => $container) {
            if(!($container instanceof ContainerInterface)) {
                $containers[$index] = new Container($container, $this);
            }
        }        
        return $this->containers = $containers;
    }

    /**
     * 
     * @api
     * @return mixed $container
     * @link
     */
    public function getContainers ()
    {
        return $this->containers;
    }

    public function getContainer($name)
    {
        if(isset($this->containers[$name])) {
            return $this->containers[$name];
        }
        return null;
    }
}
