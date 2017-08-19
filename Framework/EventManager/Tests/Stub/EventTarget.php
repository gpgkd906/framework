<?php
/**
 * PHP version 7
 * File EventTarget.php
 * 
 * @category UnitTest
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Framework\EventManager\Tests\Stub;
 
use Framework\EventManager;
use Framework\EventManager\EventTargetInterface;

/**
 * Class EventTarget
 * 
 * @category UnitTest
 * @package  Framework\EventManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class EventTarget implements EventTargetInterface
{
    use \Framework\EventManager\EventTargetTrait;

    CONST TRIGGER_TEST = 'test';
}
