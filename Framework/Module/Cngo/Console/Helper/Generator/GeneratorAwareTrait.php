<?php
/**
 * PHP version 7
 * File GeneratorAwareTrait.php
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
 * Trait GeneratorAwareTrait
 * 
 * @category Trait
 * @package  Framework\Module\Cngo\Console
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait GeneratorAwareTrait
{
    private $_Generator;

    /**
     * Method setGenerator
     *
     * @param GeneratorInterface $Generator Generator
     * 
     * @return void
     */
    public function setGenerator(GeneratorInterface $Generator)
    {
        $this->_Generator = $Generator;
    }

    /**
     * Method getGenerator
     *
     * @return GeneratorInterface
     */
    public function getGenerator()
    {
        return $this->_Generator;
    }
}
