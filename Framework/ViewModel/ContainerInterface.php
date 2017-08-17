<?php
/**
 * PHP version 7
 * File ContainerInterface.php
 * 
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\ViewModel;

/**
 * Interface ContainerInterface
 * 
 * @category Interface
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface ContainerInterface
{
    /**
     * Method setItems
     *
     * @param array $items ViewModelItems
     * 
     * @return mixed
     */
    public function setItems($items);

    /**
     * Method getItems
     *
     * @return array $items
     */
    public function getItems();

    /**
     * Method setExportView
     *
     * @param ViewModel $exportView ExportViewModel
     * 
     * @return this
     */
    public function setExportView($exportView);

    /**
     * Method getExportView
     *
     * @return ViewModel $exportView
     */
    public function getExportView();
}
