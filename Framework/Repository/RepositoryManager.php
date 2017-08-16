<?php
/**
 * PHP version 7
 * File RepositoryManager.php
 * 
 * @category Repository
 * @package  Framework\Repository
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Repository;

use Framework\ObjectManager\SingletonInterface;

/**
 * Class RepositoryManager
 * 
 * @category Class
 * @package  Framework\Repository
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class RepositoryManager implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $_entityPath = [];

    /**
     * Method addEntityPath
     *
     * @param string $path Path
     * 
     * @return this
     */
    public function addEntityPath($path)
    {
        $this->_entityPath[] = $path;
        return $this;
    }

    /**
     * Method getEntityPath
     *
     * @return array $entityPath
     */
    public function getEntityPath()
    {
        return $this->_entityPath;
    }
}
