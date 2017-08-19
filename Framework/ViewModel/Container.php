<?php
/**
 * PHP version 7
 * File Container.php
 * 
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\ViewModel;

use Framework\ViewModel\ViewModelInterface;
use Framework\ViewModel\FormViewModelInterface;
use ArrayAccess;
use Exception;

/**
 * Class Container
 * 
 * @category Class
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class Container implements 
    ContainerInterface, 
    ArrayAccess
{
    private $_items = [];
    private $_exportView = null;

    /**
     * Constructor
     *
     * @param array          $config     ContainerConfig
     * @param ViewModel|null $exportView ExportViewModel
     */ 
    public function __construct($config, $exportView = null)
    {
        $this->setExportView($exportView);
        $this->setItems($config);
        $this->getItems();
    }

    /**
     * Method setItems
     *
     * @param array $items ViewModelItems
     * 
     * @return this
     */
    public function setItems($items)
    {
        $this->_items = $items;
        return $this;
    }

    /**
     * Method getItems
     *
     * @return array $items
     */
    public function getItems()
    {
        foreach ($this->_items as $key => $item) {
            if (!$item instanceof ViewModelInterface) {
                $this->_items[$key] = $this->getViewModel($item);
            }
        }
        return $this->_items;
    }

    /**
     * Method item
     *
     * @param array|ViewModel $item ViewModelOrViewModelConfig
     * 
     * @return this
     */
    public function addItem($item)
    {
        $this->_items[] = $item;
        return $this;
    }

    /**
     * Method toString
     *
     * @return string $containerContent
     */
    public function __toString()
    {
        $exportView = $this->getExportView();
        $render = 'render';
        if ($exportView instanceof LayoutInterface) {
            $render = 'renderHtml';
        }
        //PHP7.0まで、__toStringにExceptionが発生したらFatalErrorになるのでここではエラー情報出力して自衛すること
        //Production環境まで作らないと思うが、もし作るのであれば、環境チェックも必要です
        try {
            $htmls = [];
            foreach ($this->getItems() as $item) {
                $htmls[] = call_user_func([$item, $render]);
            }
            return join('', $htmls);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
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

    /**
     * Method offsetSet
     *
     * @param integer $offset Offset
     * @param mixed   $value  ViewModelOrViewModelConfig
     * 
     * @return this
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_items[] = $value;
        } else {
            $this->_items[$offset] = $value;
        }
        return $this;
    }

    /**
     * Method offsetExists
     *
     * @param integer $offset Offset
     * 
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_items[$offset]);
    }

    /**
     * method OffsetUnset
     *
     * @param integer $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }

    /**
     * Method offsetGet
     *
     * @param integer $offset Offset
     * 
     * @return ViewModel|null $item
     */
    public function offsetGet($offset)
    {
        if (isset($this->_items[$offset])) {
            $item = $this->_items[$offset];
            if (!$item instanceof ViewModelInterface) {
                $this->_items[$offset] = $this->getViewModel($item);
            }
            return $this->_items[$offset];
        }
        return null;
    }

    /**
     * Method getViewModel
     *
     * @param array|ViewModel $item ViewModelOrViewModelConfig
     * 
     * @return ViewModel $item
     */
    private function getViewModel($item)
    {
        $exportView = $this->getExportView();
        $item['layout'] = $exportView->getLayout();
        $item['exportView'] = $exportView;
        $item = ViewModelManager::getViewModel($item);
        return $item;
    }
}
