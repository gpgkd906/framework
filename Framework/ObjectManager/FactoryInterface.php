<?php
/**
 * PHP version 7
 * File FactoryInterface.php
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ObjectManager;

/**
 * Interface FactoryInterface
 * 
 * @category Interface
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface FactoryInterface
{
    /**
     * Method create
     *
     * @param ObjectManager $ObjectManager ObjectManager
     * 
     * @return Object Object
     */
    public function create($ObjectManager);
}
