<?php
/**
 * PHP version 7
 * File AbstractEntity.php
 * 
 * @category Class
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\Repository\Doctrine;

use Std\Repository\EntityManagerAwareInterface;
use Framework\ObjectManager\ObjectManager;
use Std\ModelManager\AbstractModel;

/**
 * Class AbstractEntity
 * 
 * @category Class
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class AbstractEntity extends AbstractModel implements EntityManagerAwareInterface
{
    use \Std\Repository\EntityManagerAwareTrait;
}
