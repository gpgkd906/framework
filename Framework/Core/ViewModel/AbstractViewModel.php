<?php
namespace Framework\Core\ViewModel;

use Framework\Core\Interfaces\ViewModelInterface;
use Framework\Core\ViewModel\ViewModelManager;
use Framework\Core\Interfaces\EventInterface;
use Exception;

abstract class AbstractViewModel implements ViewModelInterface, EventInterface
{
    use \Framework\Core\EventManager\EventTrait;
    
    const renderAsHTML = "html";
    const renderAsJSON = "json";
    const renderAsXML = "xml";

    //error
    const ERROR_INVALID_RENDER_TYPE = "error: invalid render-type";
    const ERROR_INVALID_RENDER_TEMPLATE = "error: invalid render template [%s]";

    
    protected $template = null;
    protected $data = [];
    protected $layout = null;
    protected $items = [];
    protected $renderType = "html";
    protected $templateDir = null;
    private $childs = [];

    public function __construct() {
    }
    
    public function setRenderType($renderType)
    {
        $this->renderType = $renderType;
    }

    public function getRenderType()
    {
        return $this->renderType;
    }

    public function addItem($item)
    {
        $this->items[] = $item;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function getItems()
    {
        return $this->items;
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

    public function addChild(ViewModelInterface $ViewModel)
    {
        $ViewModel->setRenderType($this->getRenderType());
        $this->childs[] = $ViewModel;
    }
    
    public function getChilds()
    {
        if(empty($this->childs) && !empty($this->items)) {
            foreach($this->items as $item) {
                $this->childs[] = ViewModelManager::getViewModel($item);
            }
        }
        return $this->childs;
    }
    
    public function render($renderType = null)
    {
        if($renderType === null) {
            $renderType = $this->renderType;
        }
        switch($renderType) {
        case static::renderAsHTML:
            return $this->renderAsHTML();
            break;
        case static::renderAsJSON:
            return $this->renderAsJSON();
            break;
        case static::renderAsXML:
            return $this->renderAsXML();
            break;
        default:
            throw new Exception(self::ERROR_INVALID_RENDER_TYPE);
            break;
        }
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
            foreach($this->getChilds() as $child) {
                $htmls[] = $child->asHtml();
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

    public function renderAsHtml()
    {
        if($template = $this->getTemplateForRender()) {
            $data = $this->escapeHtml($this->getData());
            extract($data);
            require $template;
        }
        foreach($this->getChilds() as $child) {
            $child->renderAsHtml();
        }        
    }

    public function renderAsJSON()
    {
        echo $this->asJson();
    }

    public function renderAsXML()
    {
        
    }
    
    public function getLayout()
    {
        if($this->layout !== null) {
            $layout = $this->layout;
            return $layoutView = new $layout;
        }
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
            return htmlspecialchars($data,ENT_QUOTES);
        }else{
            return $data;
        }        
    }
}