<?php
/**
 * PHP version 7
 * File Test.php
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\ObjectManager\Tests\StubFactory;

use Framework\ObjectManager;
use Framework\ObjectManager\FactoryInterface;

/**
 * Class Test
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class TestFactory implements FactoryInterface
{
    private $_test;

    /**
     * Method create
     *
     * @param ObjectManager $ObjectManager ObjectManager
     *
     * @return Object Object
     */
    public function create($ObjectManager)
    {
        if ($this->_test === null) {
            $this->_test = new Test;
        }
        return $this->_test;
    }
}
