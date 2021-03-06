<?php
/**
 * PHP version 7
 * File AbstractApplication.php
 *
 * @category Module
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Application;

use Framework\Plugin\PluginManager\PluginManager;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Framework\EventManager;
use Framework\EventManager\EventTargetInterface;
use Std\Config\ConfigModel\ConfigModelInterface;
use Exception;

/**
 * Class AbstractApplication
 *
 * @category Application
 * @package  Framework\Application
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
abstract class AbstractApplication implements
    ObjectManagerAwareInterface,
    EventTargetInterface,
    ApplicationInterface
{
    use \Framework\ObjectManager\ObjectManagerAwareTrait;
    use \Framework\EventManager\EventTargetTrait;

    //
    const DEFAULT_TIMEZONE = "Asia/Tokyo";

    private $_config = null;

    /**
     * Method __construct
     *
     * @param ConfigModelInterface $config Config
     */
    public function __construct(ConfigModelInterface $config)
    {
        $this->setConfig($config);
        $objectManager = $this->getObjectManager();
        $objectManager->set(ApplicationInterface::class, $this);
        $objectManager->init();
        $this->triggerEvent(self::TRIGGER_INITED);
    }

    /**
     * Method setConfig
     *
     * @param ConfigModelInterface $config Config
     *
     * @return this
     */
    public function setConfig($config)
    {
        $this->_config = $config;
        if ($this->_config->get('timezon')) {
            date_default_timezone_set($this->_config->get('timezon'));
        } else {
            date_default_timezone_set(static::DEFAULT_TIMEZONE);
        }
        return $this;
    }

    /**
     * Method getConfig
     *
     * @param string|null $key config key
     *
     * @return mixed config
     */
    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->_config;
        } else {
            return $this->_config->get($key);
        }
    }

    /**
     * Abstract Method run
     *
     * @return void
     */
    abstract  public function run();
}
