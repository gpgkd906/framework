<?php
/**
 * PHP version 7
 * File EntityManagerAwareTrait.php
 * 
 * @category Interface
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\Repository;

use Framework\ObjectManager\ObjectManager;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;

/**
 * Trait EntityManagerAwareTrait
 * 
 * @category Trait
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait EntityManagerAwareTrait
{
    private static $_EntityManager;

    /**
     * Method setEntityManager
     *
     * @param EntityManager $EntityManager EntityManager
     * 
     * @return this
     */
    public function setEntityManager(DoctrineEntityManager $EntityManager)
    {
        self::$_EntityManager = $EntityManager;
        return $this;
    }

    /**
     * Method getEntityManager
     *
     * @return EntityManager $EntityManager
     */
    public function getEntityManager()
    {
        if (!self::$_EntityManager) {
            $this->setEntityManager(ObjectManager::getSingleton()->get(EntityManager::class));
        }
        return self::$_EntityManager;
    }
}
