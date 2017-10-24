<?php
/**
 * PHP version 7
 * File Container.php
 *
 * @category Module
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\ViewModel\Exception;

use Psr\Container\NotFoundExceptionInterface;
use Exception;

class ContainerNotFoundException extends Exception
    implements NotFoundExceptionInterface
{
}
