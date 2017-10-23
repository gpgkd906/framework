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
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Exception;
use Framework\ViewModel\Exception\ContainerNotFoundException;
use Framework\ObjectManager\ObjectManager;

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
    PsrContainerInterface,
    ViewModelManagerAwareInterface,
    ContainerInterface
{
    use ViewModelManagerAwareTrait;

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
        $htmls = [];
        foreach ($this->getItems() as $item) {
            $htmls[] = call_user_func([$item, $render]);
        }
        return join('', $htmls);
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
        $item = $this->getViewModelManager()->getViewModel($item);
        return $item;
    }

    /**
    * Finds an entry of the container by its identifier and returns it.
    *
    * @param string $id Identifier of the entry to look for.
    *
    * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
    * @throws ContainerExceptionInterface Error while retrieving the entry.
    *
    * @return mixed Entry.
    */
    public function get($id)
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }
        throw new ContainerNotFoundException("$id not founded in Container");
    }

    /**
    * Returns true if the container can return an entry for the given identifier.
    * Returns false otherwise.
    *
    * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
    * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
    *
    * @param string $id Identifier of the entry to look for.
    *
    * @return bool
    */
    public function has($id)
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            if ($item->getId() === $id) {
                return true;
            }
        }
        return false;
    }
}
