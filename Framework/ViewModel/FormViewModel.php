<?php
/**
 * PHP version 7
 * File FormViewModel.php
 *
 * @category Module
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\ViewModel;

use Framework\Router\RouterInterface;
use Framework\FormManager\FormManager;

/**
 * Class FormViewModel
 *
 * @category Class
 * @package  Framework\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class FormViewModel extends AbstractViewModel implements
    FormViewModelInterface
{
    protected $method = "post";
    protected $action = null;
    protected $useConfirm = true;
    protected $fieldset = [];
    private $_formManager = null;
    private $_form = null;

    /**
     * Method setFieldset
     *
     * @param array|string $fieldset FieldsetConfigOrFieldsetClass
     *
     * @return mixed
     */
    public function setFieldset($fieldset)
    {
        return $this->fieldset = $fieldset;
    }

    /**
     * Method getFieldset
     *
     * @return Fieldset $fieldset
     */
    public function getFieldset()
    {
        return $this->fieldset;
    }

    /**
     * Method getAction
     *
     * @return string action
     */
    public function getAction()
    {
        return $this->getRouterManager()
                    ->getMatched()
                    ->getRequestUri();
    }

    /**
     * Constructor
     *
     * @param array         $config        ViewModelConfig
     * @param ObjectManager $objectManager ObjectManager
     */
    public function init($config = [])
    {
        parent::init($config);
        $this->addEventListener(
            self::TRIGGER_INIT,
            function () {
                $classNames = explode('\\', static::class);
                $classname = array_pop($classNames);
                $localName = str_replace('ViewModel', '', $classname);
                $formId = $localName . 'Form';
                $form = $this->getFormManager()->create($formId);
                $this->setForm($form);
                $form->set('action', $this->getAction());
                $form->set('method', $this->getMethod());
                foreach ($this->getFieldset() as $fieldsetName => $fieldset) {
                    if (is_array($fieldset)) {
                        $fieldset = [
                            'name' => $fieldsetName,
                            'fieldset' => $fieldset,
                        ];
                    }
                    $form->addFieldset($fieldset);
                }
                $this->triggerEvent(self::TRIGGER_FORMINIT);
                $form->submit([$this, 'triggerForSubmit']);
                $confirm = false;
                if ($this->useConfirm) {
                    $confirm = [$this, 'triggerForConfirm'];
                }
                $form->confirm($confirm, [$this, 'triggerForComplete']);
            }
        );
    }

    /**
     * Method setForm
     *
     * @param Form $form Form
     *
     * @return this
     */
    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }

    /**
     * Method getForm
     *
     * @return Form $form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Method setFormManager
     *
     * @param FormManager $formManager FormManager
     *
     * @return this
     */
    public function setFormManager($formManager)
    {
        $this->_formManager = $formManager;
        return $this;
    }

    /**
     * Method getFormManager
     *
     * @return FormManager $formManager
     */
    public function getFormManager()
    {
        if ($this->_formManager === null) {
            $this->_formManager = $this->getObjectManager()->create(null, FormManager::class);
        }
        return $this->_formManager;
    }

    /**
     * Method triggerForSubmit
     *
     * @param array $data EventData
     *
     * @return void
     */
    public function triggerForSubmit($data)
    {
        $this->triggerEvent(self::TRIGGER_FORMSUBMIT, $data);
    }

    /**
     * Method triggerForConfirm
     *
     * @param array $data EventData
     *
     * @return void
     */
    public function triggerForConfirm($data)
    {
        $this->triggerEvent(self::TRIGGER_FORMCONFIRM, $data);
    }

    /**
     * Method triggerForComplete
     *
     * @param array $data EventData
     *
     * @return void
     */
    public function triggerForComplete($data)
    {
        $this->triggerEvent(self::TRIGGER_FORMCOMPLETE, $data);
    }

    /**
     * Method setMethod
     *
     * @param string $method request_method
     *
     * @return this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Method getMethod
     *
     * @return string $request_method
     */
    public function getMethod()
    {
        return $this->method;
    }
}
