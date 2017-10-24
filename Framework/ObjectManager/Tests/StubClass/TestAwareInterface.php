<?php
/**
 * PHP version 7
 * File TestAwareInterface.php
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ObjectManager\Tests\StubClass;

/**
 * Interface TestAwareInterface
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface TestAwareInterface
{
    /**
     * Method setTest
     *
     * @param TestInterface $Test Test
     * @return mixed
     */
    public function setTest(Test $Test);

    /**
     * Method getTest
     *
     * @return TestInterface $Test
     */
    public function getTest();
}
