<?php
/**
 * PHP version 7
 * File EntityManagerAwareInterface.php
 * 
 * @category Interface
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Repository;

use Doctrine\ORM\EntityManager;

/**
 * Interface EntityManagerAwareInterface
 * 
 * @category Interface
 * @package  Framework\Repositry
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface EntityManagerAwareInterface
{
    /**
     * Method setEntityManager
     *
     * @param EntityManager $EntityManager EntityManager
     * 
     * @return mixed
     */
    public function setEntityManager(EntityManager $EntityManager);

    /**
     * Method getEntityManager
     *
     * @return EntityManager $EntityManager
     */
    public function getEntityManager();
}
