<?php
namespace Framework\ViewModel\ViewModel;

use Framework\Application\ServiceManagerAwareInterface;
use Framework\Event\Event\EventInterface;
use Framework\Model\ModelInterface;
use Framework\ViewModel\ViewModel\ViewHelper;
use Exception;

abstract class AbstractViewModel implements ViewModelInterface, EventInterface, ServiceManagerAwareInterface
{
    use \Framework\Event\Event\EventTrait;
    use \Framework\Application\ServiceManagerAwareTrait;
    
    const renderAsHTML = "html";
    const renderAsJSON = "json";
    const renderAsXML = "xml";

    //error
    const ERROR_INVALID_RENDER_TYPE = "error: invalid render-type";
    const ERROR_INVALID_RENDER_TEMPLATE = "error: invalid render template [%s] for ViewModel [%s]";
    //trigger
    const TRIGGER_RENDER = "Render";
    const TRIGGER_DISPLAY = "Display";
    
    protected $template = null;
    protected $data = [];
    protected $renderType = "html";
    protected $templateDir = null;
    protected $Model = null;
    private $childs = [];
    private $id = null;
    private $exportView = null;
    private $entities = null;
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
    private $containers = [];

    /**
     *
     * @api
     * @var mixed $viewHelper 
     * @access private
     * @link
     */
    private $viewHelper = null;

    /**
     *
     * @api
     * @var mixed $numberFormatter 
     * @access private
     * @link
     */
    private $numberFormatter = null;

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

    public function __construct($config, $serviceManager) {
        $config = array_merge_recursive($this->getConfig(), $config);
        $this->setConfig($config);
        $this->setServiceManager($serviceManager);
        if($this->id === null) {
            if(isset($config["id"])) {
                $this->id = $config["id"];
            } else {
                $this->id = self::getIncrementId();
            }
        }
        //data:template
        if(isset($config["data"])) {
            $this->setData($config["data"]);
        }
        //Model
        if(isset($config['model'])) {
            $this->setModel($config['model']);
        }
        //layout;
        if(isset($config['layout'])) {
            $layout = $config['layout'];
            if($layout instanceof LayoutInterface) {
                $this->setLayout($layout, $config);
            } else {
                $this->setLayout($layout::getSingleton(), $config);
            }
        }
        if(isset($config['exportView']) && $config['exportView'] instanceof ViewModelInterface) {
            $this->setExportView($config['exportView']);
        }
        //container:template
        if(isset($config['container'])) {
            $this->setContainers($config['container']);
        }
        //ViewModel Event init
        foreach($this->listeners as $event => $listener) {
            $this->Addeventlistener($event, [$this, $listener]);
        }
        if(isset($config['listeners'])) {
            foreach($config['listeners'] as $event => $listener) {
                $this->addEventListener($event, $listener);
            }
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
        throw new Exception(sprintf(self::ERROR_INVALID_RENDER_TEMPLATE, $template, static::class));
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData($key = null)
    {
        if($key !== null) {
            if(isset($this->data[$key])) {
                return $this->data[$key];
            } elseif ($this->getExportView()) {
                return $this->getExportView()->getData($key);
            } 
            return null;
        }
        return $this->data;
    }

    public function setEntities($entities)
    {
        $this->entities = $entities;
    }

    public function getEntities()
    {
        if(empty($this->entities)) {
            if($this->getLocalModel()) {
                $this->entities = (array)$this->getLocalModel()->getEntities();
            } elseif ($this->getExportView()) {
                return $this->getExportView()->getEntities();
            }            
        }
        return $this->entities;
    }
    
    public function getChild($id)
    {
        $childs = $this->getChilds();
        $targetView = ViewModelManager::getViewById($id);
        if(in_array($targetView, $childs)) {
            return $targetView;
        } else {
            return null;
        }
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
    public function setLayout (LayoutInterface $layout, $config = null)
    {
        if($config === null) {
            $config = $this->getConfig();
        }
        if(isset($config['script'])) {
            foreach($config['script'] as $script) {
                $layout->registerScript($script);
            }
        }
        if(isset($config['style'])) {
            foreach($config['style'] as $style) {
                $layout->registerStyle($style);
            }
        }
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
    
    /**
     * 
     * @api
     * @param mixed $model
     * @return mixed $model
     * @link
     */
    public function setModel ($model)
    {
        if(!$model instanceof ModelInterface) {
            if(is_subclass_of($model, ModelInterface::class)) {
                $model = $this->getServiceManager()->get('Model', $model);
            } elseif($model instanceof \Closure) {
                $model = call_user_func($model);
            }           
        }
        return $this->model = $model;
    }

    /**
     * 
     * @api
     * @return mixed $model
     * @link
     */
    public function getModel ()
    {
        if(!isset($this->model)) {
            if($this->getExportView()) {
                return $this->getExportView()->getModel();
            }
        }
        return $this->model;
    }

    /**
     * 
     * @api
     * @return mixed $model
     * @link
     */
    private function getLocalModel ()
    {
        if(!isset($this->model)) {
            return null;
        }
        return $this->model;
    }

    public function linkto($target, $param = [])
    {
        if(!empty($param)) {
            $target = $target . '/' . join('/', $param);
        }
        $link = ViewModelManager::getBasePath() . '/' . $target;
        return str_replace('//', '/', $target);
    }

    /**
     * 
     * @api
     * @param mixed $viewHelper
     * @return mixed $viewHelper
     * @link
     */
    public function setViewHelper ($viewHelper)
    {
        return $this->viewHelper = $viewHelper;
    }

    /**
     * 
     * @api
     * @return mixed $viewHelper
     * @link
     */
    public function getViewHelper ()
    {
        if($this->viewHelper === null) {
            $this->viewHelper = ViewHelper::getSingleton();           
        }
        return $this->viewHelper;
    }

    /**
     * 
     * @api
     * @param mixed $exportView
     * @return mixed $exportView
     * @link
     */
    public function setExportView ($exportView)
    {
        return $this->exportView = $exportView;
    }

    /**
     * 
     * @api
     * @return mixed $exportView
     * @link
     */
    public function getExportView ()
    {
        return $this->exportView;
    }

    /**
     * 
     * @api
     * @param mixed $numberFormatter
     * @return mixed $numberFormatter
     * @link
     */
    public function setNumberFormatter ($numberFormatter)
    {
        return $this->numberFormatter = $numberFormatter;
    }

    /**
     * 
     * @api
     * @return mixed $numberFormatter
     * @link
     */
    public function getNumberFormatter ()
    {
        if($this->numberFormatter === null) {
            $this->numberFormatter = NumberFormatter::getSingleton();
        }
        return $this->numberFormatter;
    }
}
