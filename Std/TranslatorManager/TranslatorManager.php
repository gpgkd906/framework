<?php
/**
 * PHP version 7
 * File TranslatorManager.php
 *
 * @category Module
 * @package  Std\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\TranslatorManager;

use Framework\ObjectManager\SingletonInterface;
use Zend\I18n\Translator\Resources;
use Zend\I18n\Translator\Translator;
use Std\Config\ConfigModel;
use Zend\Cache\StorageFactory;

/**
 * Class TranslatorManager
 *
 * @category Class
 * @package  Std\TranslatorManager
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class TranslatorManager implements
    TranslatorManagerInterface,
    SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $_Translators;
    private $_config;

    /**
     * Method getTranslator
     *
     * @param string $type Type
     *
     * @return Translator $translator
     */
    public function getTranslator($type)
    {
        if (isset($this->_Translators[$type])) {
            return $this->_Translators[$type];
        }
        $translator = $this->_createTranslator();
        if (self::VALIDATOR === $type) {
            $translator->addTranslationFilePattern(
                'phpArray',
                Resources::getBasePath(),
                Resources::getPatternForValidator()
            );
        }
        $translator->setLocale($this->_getConfig()->get('default'));
        $cacheOption = $this->_getConfig()->get('cache');
        if ($cacheOption) {
            $cacheOption['adapter']['options']['namespace'] .= $type;
            $cacheAdapter = StorageFactory::factory($cacheOption);
            $translator->setCache($cacheAdapter);
        }
        $this->_Translators[$type] = $translator;
        return $translator;
    }

    /**
     * Method createTranslator
     *
     * @return Translator $translator
     */
    private function _createTranslator()
    {
        return new Translator();
    }

    /**
     * Method getConfig
     *
     * @return ConfigModel $config
     */
    private function _getConfig()
    {
        if ($this->_config === null) {
            $this->_config = ConfigModel::getConfigModel([
                "scope" => 'translation',
                "property" => ConfigModel::READONLY,
            ]);
        }
        return $this->_config;
    }
}
