<?php
/**
 * PHP version 7
 * File AbstractViewModel.php
 *
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\ViewModel;

use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\EventManager\EventTargetInterface;
use Framework\ViewModel\Helper\ViewHelper;
use Framework\ViewModel\Helper\NumberFormatter;
use Framework\Router\RouterManagerAwareInterface;
use Exception;

/**
 * Class AbstractViewModel
 *
 * @category Class
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractViewModel implements
    ViewModelInterface,
    ViewModelManagerAwareInterface,
    EventTargetInterface,
    RouterManagerAwareInterface,
    ObjectManagerAwareInterface
{
    use \Framework\EventManager\EventTargetTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\Router\RouterManagerAwareTrait;
    use ViewModelManagerAwareTrait;

    //trigger
    const TRIGGER_BEFORE_RENDER = "before Render";
    const TRIGGER_AFTER_RENDER = "after Render";

    protected $template = null;
    protected $data = [];
    protected $templateDir = null;
    protected $Model = null;
    protected $config = [];
    protected $listeners = [];
    private $_id = null;
    private $_exportView = null;
    private $_layout = null;
    private $_containers = [];
    private $_content = null;

    /**
     * Method setConfig
     *
     * @param array $config Config
     *
     * @return this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Method getConfig
     *
     * @return array $config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Constructor
     *
     * @param array              $config        viewModelConfig
     */
    public function init($config = [])
    {
        $config = array_merge_recursive($this->getConfig(), $config);
        $this->setConfig($config);
        if (isset($config["id"])) {
            $this->_id = $config["id"];
        } else {
            $this->_id =  'View-' . $this->getViewModelManager()->getIncrementId();
        }
        //data:template
        if (isset($config["data"])) {
            $this->setData($config["data"]);
        }
        //layout;
        if (isset($config['layout'])) {
            $layout = $config['layout'];
            if ($layout instanceof LayoutInterface) {
                $this->setLayout($layout, $config);
            } else {
                $this->setLayout(
                    $this->getViewModelManager()->getViewModel([
                        'viewModel' => $layout
                    ]),
                    $config
                );
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

    /**
     * Method getId
     *
     * @return string ViewModelId
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Method setTemplate
     *
     * @param string $template Template
     *
     * @return this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Method getTemplate
     *
     * @return string $template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Method setTemplateDir
     *
     * @param string $templateDir TemplateDir
     *
     * @return this
     */
    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;
        return $this;
    }

    /**
     * Method getTemplateDir
     *
     * @return string $templateDir
     */
    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    /**
     * Method getTemplateForRender
     *
     * @return string realTemplateFile
     */
    public function getTemplateForRender()
    {
        $template = $this->getTemplate();
        if ($template === null) {
            return null;
        }
        if (is_file($template)) {
            return realpath($template);
        }
        $template = $this->getTemplateDir() . $this->getTemplate();
        if (is_file($template)) {
            return realpath($template);
        }
        throw new Exception(sprintf(self::ERROR_INVALID_RENDER_TEMPLATE, $template, static::class));
    }

    /**
     * Method setData
     *
     * @param array $data Data
     *
     * @return this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Method
     *
     * @param null|string $key DataKey
     *
     * @return mixed
     */
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

    /**
     * Method render
     *
     * @return string responseContent
     */
    public function render()
    {
        if (!$this->getExportView() && $this->getLayout()) {
            $Layout = $this->getLayout();
            $Layout->getContainer('Main')->addItem($this);
            $Layout->setData($this->getData());
            $responseContent = $Layout->renderHtml();
        } else {
            $responseContent = $this->renderHtml();
        }
        return $responseContent;
    }

    /**
     * Method renderHtml
     *
     * @return string contents
     */
    public function renderHtml()
    {
        $this->triggerEvent(self::TRIGGER_BEFORE_RENDER);
        $render = $this->getViewModelManager()->getRenderer();
        $this->setContent($render->render($this));
        $this->triggerEvent(self::TRIGGER_AFTER_RENDER);
        return $this->getContent();
    }

    /**
     * Meythod setLayout
     *
     * @param LayoutInterface $layout Layout
     * @param array|null      $config config
     *
     * @return this
     */
    public function setLayout(LayoutInterface $layout, $config = null)
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
        $this->_layout = $layout;
        return $this;
    }

    /**
     * Method getLayout
     *
     * @return LayoutInterface $layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Method setContainers
     *
     * @param array $containers ContainerArray
     * @return this
     */
    public function setContainers($containers)
    {
        foreach ($containers as $index => $container) {
            if (!($container instanceof ContainerInterface)) {
                $containers[$index] = $this->getObjectManager()->create(
                    null,
                    function () use ($container) {
                        return new Container($container, $this);
                    }
                );
            }
        }
        $this->_containers = $containers;
        return $this;
    }

    /**
     * Method getContainers
     *
     * @return arrays $containers
     */
    public function getContainers()
    {
        return $this->_containers;
    }

    /**
     * Method getContainer
     *
     * @param string $name ContainerName
     *
     * @return Container|null $container
     */
    public function getContainer($name)
    {
        if (!isset($this->_containers[$name])) {
            $this->_containers[$name] = new Container([], $this);
        }
        return $this->_containers[$name];
    }

    /**
     * Method setcontent
     *
     * @param string $content content
     *
     * @return this
     */
    public function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    /**
     * Method getcontent
     *
     * @return string $content
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Method setExportView
     *
     * @param ViewModel $exportView ExportViewModel
     *
     * @return this
     */
    public function setExportView($exportView)
    {
        $this->_exportView = $exportView;
        return $this;
    }

    /**
     * Method getExportView
     *
     * @return ViewModel $exportView
     */
    public function getExportView()
    {
        return $this->_exportView;
    }
}
