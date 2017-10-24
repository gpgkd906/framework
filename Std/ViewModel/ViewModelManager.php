<?php
/**
 * PHP version 7
 * File ViewModelManager.php
 *
 * @category Module
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\ViewModel;

use Framework\EventManager\EventTargetInterface;
use Framework\ObjectManager\SingletonInterface;
use Framework\ObjectManager\ObjectManagerAwareInterface;
use Exception;

/**
 * Class ViewModelManager
 *
 * @category Class
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class ViewModelManager implements
    SingletonInterface,
    EventTargetInterface,
    ObjectManagerAwareInterface,
    ViewModelManagerInterface
{
    use \Framework\ObjectManager\SingletonTrait;
    use \Framework\EventManager\EventTargetTrait;
    use \Framework\ObjectManager\ObjectManagerAwareTrait;

    //ERROR
    const ERROR_INVALID_VIEWMODEL_CONFIG = "error: invalid viewmodel config";
    const ERROR_INVALID_VIEWMODEL = "error: invalid viewmodelname: %s";
    const ERROR_VIEWMODEL_DEFINED_ID = "error: viewId [%s] was defined before, change some new ID";
    //EVENT
    const TRIGGER_BEFORE_BUILD = 'beforeBuild';
    const TRIGGER_AFTER_BUILD = 'afterBuild';

    private $_viewModelPool = [];
    private $_templateDir = null;
    private $_incrementId = 0;
    private $_basePath = null;
    private $_renderer = null;

    /**
     * Method setBasePath
     *
     * @param string $basePath basePath
     *
     * @return void
     */
    public function setBasePath($basePath)
    {
        $this->_basePath = $basePath;
    }

    /**
     * Method getbasePath
     *
     * @return string $basePath
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Method setTemplateDir
     *
     * @param string $templateDir templateDir
     *
     * @return void
     */
    public function setTemplateDir(string $templateDir)
    {
        $this->_templateDir = $templateDir;
    }

    /**
     * Method getTemplateDir
     *
     * @return string $templateDir
     */
    public function getTemplateDir()
    {
        return $this->_templateDir;
    }

    /**
     * Method getViewModel
     *
     * @param array $config ViewModelConfig
     *
     * @return ViewModel $viewModel
     */
    public function getViewModel(array $config)
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

        $ViewModel = $this->getObjectManager()->create(null, $viewModelName);
        $ViewModel->init($config);
        if ($ViewModel->getTemplateDir() === null) {
            $ViewModel->setTemplateDir($this->getTemplateDir());
        }
        if ($ViewModel->getRenderer() === null) {
            $ViewModel->setRenderer($this->getRenderer());
        }
        $this->addView($ViewModel);
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
    public function addView(ViewModelInterface $viewModel)
    {
        $viewId = $viewModel->getId();
        if (isset($this->_viewModelPool[$viewId])) {
            throw new Exception(sprintf(self::ERROR_VIEWMODEL_DEFINED_ID, $viewId));
        }
        $this->_viewModelPool[$viewId] = $viewModel;
    }

    /**
     * Method getViewById
     *
     * @param string $viewId ViewModelId
     *
     * @return ViewModel $viewModel
     */
    public function getViewById($viewId)
    {
        if (isset($this->_viewModelPool[$viewId])) {
            return $this->_viewModelPool[$viewId];
        }
    }

    /**
     * Method getIncrementId
     *
     * @return void
     */
    public function getIncrementId()
    {
        $this->_incrementId ++;
        return $this->_incrementId;
    }

    /**
     * Method escapeHtml
     *
     * @param array $data Data
     *
     * @return mixed
     */
    public function escapeHtml($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->escapeHtml($value);
            }
            return $data;
        } elseif (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES);
        } else {
            return $data;
        }
    }

    public function render(ViewModelInterface $viewModel)
    {
        $this->triggerEvent(self::TRIGGER_BEFORE_BUILD);
        $response = $viewModel->render();
        $this->triggerEvent(self::TRIGGER_AFTER_BUILD);
        echo $response;
    }

    public function getRenderer()
    {
        return $this->_renderer;
    }

    public function setRenderer(RendererInterface $Renderer)
    {
        $this->_renderer = $Renderer;
    }
}
