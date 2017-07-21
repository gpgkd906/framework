<?php
namespace Framework\ViewModel;

use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\EventManager\EventTargetInterface;
use Framework\Model\ModelInterface;
use Framework\ViewModel\Helper\ViewHelper;
use Framework\ViewModel\Helper\NumberFormatter;
use Framework\Router\RouterAwareInterface;
use Exception;

abstract class AbstractViewModel implements ViewModelInterface, EventTargetInterface, ObjectManagerAwareInterface
{
    use \Framework\EventManager\EventTargetTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\Router\RouterAwareTrait;

    const RENDER_AS_HTML = "html";
    const RENDER_AS_JSON = "json";

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
    private static $incrementId = 0;
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

    private static function getIncrementId()
    {
        self::$incrementId ++;
        return "ViewModel_" . self::$incrementId;
    }

    public function __construct($config = [], $ObjectManager = null)
    {
        $config = array_merge_recursive($this->getConfig(), $config);
        $this->setConfig($config);
        if ($ObjectManager) {
            $this->setObjectManager($ObjectManager);
        }
        if (isset($config["id"])) {
            $this->id = $config["id"];
        } else {
            $this->id = self::getIncrementId();
        }
        //data:template
        if (isset($config["data"])) {
            $this->setData($config["data"]);
        }
        //Model
        if (isset($config['model'])) {
            $this->setModel($config['model']);
        }
        //layout;
        if (isset($config['layout'])) {
            $layout = $config['layout'];
            if ($layout instanceof LayoutInterface) {
                $this->setLayout($layout, $config);
            } else {
                $this->setLayout($layout::getSingleton(), $config);
            }
        }
        if (isset($config['exportView']) && $config['exportView'] instanceof ViewModelInterface) {
            $this->setExportView($config['exportView']);
        }
        //container
        if (isset($config['container'])) {
            $this->setContainers($config['container']);
        }
        //ViewModel Event init
        foreach ($this->listeners as $event => $listener) {
            $this->addEventListener($event, [$this, $listener]);
        }
        if (isset($config['listeners'])) {
            foreach ($config['listeners'] as $event => $listener) {
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
        if ($template === null) {
            return null;
        }
        if (is_file($template)) {
            return $template;
        }
        $template = $this->getTemplateDir() . $this->getTemplate();
        if (is_file($template)) {
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
        if ($key !== null) {
            if (isset($this->data[$key])) {
                return $this->data[$key];
            } elseif ($this->getExportView()) {
                return $this->getExportView()->getData($key);
            }
            return null;
        }
        return $this->data;
    }

    public function getChild($id)
    {
        $childs = $this->getChilds();
        $targetView = ViewModelManager::getViewById($id);
        if (in_array($targetView, $childs)) {
            return $targetView;
        } else {
            return null;
        }
    }

    public function getChilds()
    {
        if (empty($this->childs)) {
            foreach ($this->getContainers() as $container) {
                $this->childs += $container->getItems();
            }
        }
        return $this->childs;
    }

    public function render($renderType = null)
    {
        $this->triggerEvent(self::TRIGGER_RENDER);
        if ($renderType === null) {
            $renderType = $this->renderType;
        }
        switch($renderType) {
        case static::RENDER_AS_JSON:
            $display = $this->asJson();
            break;
        case static::RENDER_AS_HTML:
        default:
            if (!$this->getExportView() && $this->getLayout()) {
                $Layout = $this->getLayout();
                $Layout->getContainer('Main')->addItem($this);
                $Layout->setData($this->getData());
                $display = $Layout->asHtml();
            } else {
                $display = $this->asHtml();
            }
            break;
        }
        $this->triggerEvent(self::TRIGGER_DISPLAY);
        return $display;
    }

    public function asHtml()
    {
        $htmls = [];
        $template = $this->getTemplateForRender();
        ob_start();
        $data = $this->escapeHtml($this->getData());
        extract($data);
        echo '<!-- ' . static::class . ' start render-->', PHP_EOL;
        require $template;
        echo '<!-- ' . static::class . ' end render-->';
        $htmls[] = ob_get_contents();
        ob_end_clean();
        return join("", $htmls);
    }

    public function asJson()
    {
        $data = [
            "data" => $this->getData(),
            "childrens" => []
        ];
        foreach ($this->getChilds() as $child) {
            $subData = $child->getData();
            if (!empty($subData)) {
                $data["childrens"][] = $subData;
            }
        }
        return json_encode($data);
    }

    public function __toString()
    {
        return $this->render();
    }

    public static function escapeHtml($data)
    {
        if (is_array($data)){
            foreach ($data as $key => $value){
                $data[$key] = self::escapeHtml($value);
            }
            return $data;
        } elseif (is_string($data)) {
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
        if ($config === null) {
            $config = $this->getConfig();
        }
        if (isset($config['script'])) {
            foreach ($config['script'] as $script) {
                $layout->registerScript($script);
            }
        }
        if (isset($config['style'])) {
            foreach ($config['style'] as $style) {
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
        foreach ($containers as $index => $container) {
            if (!($container instanceof ContainerInterface)) {
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
        if (isset($this->containers[$name])) {
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
        if (!$model instanceof ModelInterface) {
            if (class_exists($model)) {
                $model = $this->getObjectManager()->get($model);
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
    private function getModel ()
    {
        if (!isset($this->model)) {
            return null;
        }
        return $this->model;
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

    public function getViewHelper()
    {
        return ViewHelper::getSingleton();
    }
}
