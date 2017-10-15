<?php
/**
 * PHP version 7
 * File ViewModelManager.php
 *
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ViewModel;

use Framework\EventManager\EventTargetInterface;
use Exception;

/**
 * Class ViewModelManager
 *
 * @category Class
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ViewModelManager implements ViewModelManagerInterface
{
    const ERROR_INVALID_VIEWMODEL_CONFIG = "error: invalid viewmodel config";
    const ERROR_INVALID_VIEWMODEL = "error: invalid viewmodelname: %s";
    const ERROR_INVALID_TEMPLATE_VIEWMODEL = "error: invalid template viewModel";
    const ERROR_VIEWMODEL_DEFINED_ID = "error: viewId [%s] was defined before, change some new ID";

    private static $_viewModelPool = [];
    private static $_namespace = null;
    private static $_templateDir = null;
    private static $_incrementId = 0;
    private static $_basePath = null;
    private static $_objectManager = null;

    /**
     * Method setObjectManager
     *
     * @param ObjectManager $objectManager ObjectManager
     *
     * @return void
     */
    public static function setObjectManager($objectManager)
    {
        self::$_objectManager = $objectManager;
    }

    /**
     * Method getObjectManager
     *
     * @return ObjectManager $objectManager
     */
    public static function getObjectManager()
    {
        return self::$_objectManager;
    }

    /**
     * Method setBasePath
     *
     * @param string $basePath basePath
     *
     * @return void
     */
    public static function setBasePath($basePath)
    {
        self::$_basePath = $basePath;
    }

    /**
     * Method getbasePath
     *
     * @return string $basePath
     */
    public static function getBasePath()
    {
        return self::$_basePath;
    }

    /**
     * Method setTemplateDir
     *
     * @param string $templateDir templateDir
     *
     * @return void
     */
    public static function setTemplateDir($templateDir)
    {
        self::$_templateDir = $templateDir;
    }

    /**
     * Method getTemplateDir
     *
     * @return string $templateDir
     */
    public static function getTemplateDir()
    {
        return self::$_templateDir;
    }

    /**
     * Method getViewModel
     *
     * @param array $config ViewModelConfig
     *
     * @return ViewModel $viewModel
     */
    public static function getViewModel($config)
    {
        if ($config instanceof ViewModelInterface) {
            return $config;
        }
        //throw exception if not set
        if (!isset($config["viewModel"])) {
            throw new Exception(sprintf(self::ERROR_INVALID_VIEWMODEL_CONFIG));
        }
        $requestName = $config["viewModel"];
        $viewModelName = $requestName;

        $ViewModel = self::getObjectManager()->create(null, function () use ($viewModelName, $config) {
            return new $viewModelName($config);
        });
        if ($ViewModel->getTemplateDir() === null) {
            $ViewModel->setTemplateDir(self::getTemplateDir());
        }
        self::addView($ViewModel);
        $ViewModel->triggerEvent(EventTargetInterface::TRIGGER_INIT);
        return $ViewModel;
    }

    /**
     * Method addView
     *
     * @param ViewModelInterface $viewModel ViewModel
     *
     * @return void
     */
    public static function addView(ViewModelInterface $viewModel)
    {
        $viewId = $viewModel->getId();
        if (isset(self::$_viewModelPool[$viewId])) {
            throw new Exception(sprintf(self::ERROR_VIEWMODEL_DEFINED_ID, $viewId));
        }
        self::$_viewModelPool[$viewId] = $viewModel;
    }

    /**
     * Method getViewById
     *
     * @param string $viewId ViewModelId
     *
     * @return ViewModel $viewModel
     */
    public static function getViewById($viewId)
    {
        if (isset(self::$_viewModelPool[$viewId])) {
            return self::$_viewModelPool[$viewId];
        }
    }

    /**
     * Method getIncrementId
     *
     * @return void
     */
    public static function getIncrementId()
    {
        self::$_incrementId ++;
        return "ViewModel_" . self::$_incrementId;
    }

    /**
     * Method escapeHtml
     *
     * @param array $data Data
     *
     * @return mixed
     */
    public static function escapeHtml($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::escapeHtml($value);
            }
            return $data;
        } elseif (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES);
        } else {
            return $data;
        }
    }
}
