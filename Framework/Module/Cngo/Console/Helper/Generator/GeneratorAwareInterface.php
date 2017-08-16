<?php
/**
 * PHP version 7
 * File GeneratorAwareInterface.php
 * 
 * @category Module
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Module\Cngo\Console\Helper\Generator;

/**
 * Interface GeneratorAwareInterface
 * 
 * @category Interface
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface GeneratorAwareInterface
{
    /**
     * Method setGenerator
     *
     * @param GeneratorInterface $Generator Generator
     * 
     * @return void
     */
    public function setGenerator(GeneratorInterface $Generator);

    /**
     * Method getGenerator
     *
     * @return GeneratorInterface
     */
    public function getGenerator();
}
