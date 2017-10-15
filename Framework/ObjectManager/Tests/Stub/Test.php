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
namespace Framework\ObjectManager\Tests\Stub;

use Framework\ObjectManager;

/**
 * Class Test
 *
 * @category UnitTest
 * @package  Framework\ObjectManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class Test implements TestInterface
{
    static $_count = 0;

    public function __construct()
    {
        self::$_count ++;
    }

    public function getCount()
    {
        return self::$_count;
    }
}
