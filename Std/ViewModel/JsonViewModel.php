<?php
/**
 * PHP version 7
 * File JsonViewModel.php
 *
 * @category Module
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\ViewModel;

/**
 * Class JsonViewModel
 *
 * @category Class
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class JsonViewModel extends AbstractViewModel
{
    /**
     * Method render
     *
     * @return string $jsonString
     */
    public function render()
    {
        return json_encode($this->getData());
    }
}
