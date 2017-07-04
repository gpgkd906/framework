<?php
namespace Framework\ViewModel\ViewModel;

use Framework\ViewModel\ViewModel\ViewModelInterface;
use Framework\ViewModel\ViewModel\FormViewModelInterface;
use Framework\ViewModel\ViewModel\SubFormViewModel;
use ArrayAccess;
use Exception;
    
class Container implements ContainerInterface, ArrayAccess
{

    /**
     *
     * @api
     * @var mixed $items 
     * @access private
     * @link
     */
    private $items = [];

    /**
     *
     * @api
     * @var mixed $exportView 
     * @access private
     * @link
     */
    private $exportView = null;

    public function __construct($config, $exportView = null)
    {
        $this->setExportView($exportView);
        $this->setItems($config);
        $this->getItems();
    }
    
    /**
     * 
     * @api
     * @param mixed $renderType
     * @return mixed $renderType
     * @link
     */
    public function setRenderType ($renderType)
    {
        return $this->renderType = $renderType;
    }

    /**
     * 
     * @api
     * @return mixed $renderType
     * @link
     */
    public function getRenderType ()
    {
        return $this->renderType;
    }
    
    /**
     * 
     * @api
     * @param mixed $items
     * @return mixed $items
     * @link
     */
    public function setItems ($items)
    {
        return $this->items = $items;
    }

    /**
     * 
     * @api
     * @return mixed $items
     * @link
     */
    public function getItems ()
    {
        foreach($this->items as $key => $item) {
            if(!$item instanceof ViewModelInterface) {
                $this->items[$key] = $this->getViewModel($item);
            }
        }
        return $this->items;
    }

    /**
     * 
     * @api
     * @param   
     * @param    
     * @return
     * @link
     */
    public function addItem ($item, $forceView = false)
    {
        if($forceView) {
            $item = $this->getViewModel($item);
        }
        $this->items[] = $item;
    }
    
    public function __toString()
    {
        //PHP7.0まで、__toStringにExceptionが発生したらFatalErrorになるのでここではエラー情報出力して自衛すること
        //Production環境まで作らないと思うが、もし作るのであれば、環境チェックも必要です
        try {
            $htmls = [];
            foreach($this->getItems() as $item) {            
                $htmls[] = $item->render();
            }
            return join('', $htmls);
        } catch(Exception $e) {
            echo nl2br($e);
        }
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

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    public function offsetExists($offset) {
        return isset($this->items[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->items[$offset]);
    }
    
    public function offsetGet($offset) {
        if(isset($this->items[$offset])) {
            $item = $this->items[$offset];
            if(!$item instanceof ViewModelInterface) {
                $this->items[$offset] = $this->getViewModel($item);
            }
            return $this->items[$offset];
        }
        return null;
    }

    private function getViewModel($item)
    {
        $exportView = $this->getExportView();
        $item['layout'] = $exportView->getLayout();
        $item['exportView'] = $exportView;
        $item = ViewModelManager::getViewModel($item);
        $item->setRenderType($exportView->getRenderType());
        return $item;
    }
}
