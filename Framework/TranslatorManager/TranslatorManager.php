<?php
declare(strict_types=1);

namespace Framework\TranslatorManager;

use Framework\ObjectManager\SingletonInterface;
use Zend\I18n\Translator\Resources;
use Zend\I18n\Translator\Translator;
use Framework\Config\ConfigModel;
use Zend\Cache\StorageFactory;

class TranslatorManager implements TranslatorManagerInterface, SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    private $Translators;
    private $config;

    public function getTranslator($type)
    {
        if (isset($this->Translators[$type])) {
            return $this->Translators[$type];
        }
        $translator = $this->createTranslator();
        if (self::VALIDATOR === $type) {
            $translator->addTranslationFilePattern(
                'phpArray',
                Resources::getBasePath(),
                Resources::getPatternForValidator()
            );
        }
        $translator->setLocale($this->getConfig()->get('default'));
        $cacheOption = $this->getConfig()->get('cache');
        if ($cacheOption) {
            $cacheOption['adapter']['options']['namespace'] .= $type;
            $cacheAdapter = StorageFactory::factory($cacheOption);
            $translator->setCache($cacheAdapter);
        }
        $this->Translators[$type] = $translator;
        return $translator;
    }

    private function createTranslator()
    {
        return new Translator();
    }

    private function getConfig()
    {
        if ($this->config === null) {
            $this->config = ConfigModel::getConfigModel([
                "scope" => 'translation',
                "property" => ConfigModel::READONLY,
            ]);
        }
        return $this->config;
    }
}
