<?php
/**
 * PHP version 7
 * File {Module}AwareTrait.php
 * 
 * @category Controller
 * @package  Framework{Namespace}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework{Namespace};
use Framework\ObjectManager\ObjectManager;

/**
 * Trait {Module}AwareTrait
 * 
 * @category Trait
 * @package  Framework{Namespace}
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait {Module}AwareTrait
{
    private static ${Module};

    /**
     * Method index
     *
     * @param {Module}Interface ${Module}
     * 
     * @return Object
     */
    public function set{Module}({Module}Interface ${Module})
    {
        self::${Module} = ${Module};
        return $this;
    }

    /**
     * Method index
     *
     * @return {Module}Interface {Module}
     */
    public function get{Module}()
    {
        if (!self::${Module}) {
            $this->set{Module}(ObjectManager::getSingleton()->get({Module}Interface::class, {Module}::class));
        }
        return self::${Module};
    }
}
