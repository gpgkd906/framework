<?php
/**
 * PHP version 7
 * File TestAwareTrait.php
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ObjectManager\Tests\Stub;

use Framework\ObjectManager\ObjectManager;

/**
 * Trait TestAwareTrait
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait TestAwareTrait
{
    private static $_Test;

    /**
     * Method setTest
     *
     * @param TestInterface $Test Test
     * @return this
     */
    public function setTest(TestInterface $Test)
    {
        self::$_Test = $Test;
        return $this;
    }

    /**
     * Method getTest
     *
     * @return TestInterface $Test
     */
    public function getTest()
    {
        return self::$_Test;
    }
}
