<?php
/**
 * PHP version 7
 * File CacheManager.php
 *
 * @category Service
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\CacheManager;

use Framework\ObjectManager\SingletonInterface;
use Std\Config\ConfigModel;
use Zend\Cache\StorageFactory;
use Zend\Cache\Storage\FlushableInterface;

/**
 * Interface CacheManager
 *
 * @category Interface
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class CacheManagerMocker extends CacheManager
{
    /**
     * 委托CacheManager自动分配一个缓存器。
     * 逻辑侧可以指定缓存类型，但不制定具体的缓存设定。
     * 逻辑侧也可以省略缓存类型，这种情况下，由CacheManager自动选择默认缓存类型
     * 逻辑侧委托已经存在的缓存器会产生错误。
     *
     * @param [type] $section
     * @param [type] $options
     * @return void
     */
    public function delegate($section, $options = null)
    {
        $config = $this->getConfig();
        $delegate = $config->get('delegate');
        if (!is_array($options)) {
            if ($options === null || !isset($delegate[$options])) {
                $options = $delegate['default'];
            }
            $delegateOptions = $delegate['adapter'][$options];
            $delegateOptions['adapter']['options']['namespace'] .= $section;
            $options = $delegateOptions;
        }
        $this->registerCache($section, $options);
        return $this->getCache($section);
    }
}
