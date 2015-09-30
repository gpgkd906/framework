<?php
namespace Framework\ViewModel\ViewModel;

use Exception;

class Container implements ContainerInterface {

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
        $exportViewRenderType = $this->getExportView()->getRenderType();
        $exportViewLayout = $this->getExportView()->getLayout();
        foreach($this->items as $key => $item) {
            $this->items[$key] = ViewModelManager::getViewModel($item);
            $this->items[$key]->setRenderType($exportViewRenderType);
            $this->items[$key]->setLayout($exportViewLayout);
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
            $item = ViewModelManager::getViewModel($item);
            $item->setRenderType($this->getExportView()->getRenderType());
            $item->setLayout($this->getExportView()->getLayout());
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
            echo $e;
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
}
