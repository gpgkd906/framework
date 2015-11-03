<?php

namespace Framework\ViewModel\ViewModel;

use Form2\FormManager;
use Form2\Validator;

class FormViewModel extends AbstractViewModel implements FormViewModelInterface
{
    const TRIGGER_FORMINIT = 'forminit';
    const TRIGGER_FORMSUBMIT = 'Submit';
    const TRIGGER_FORMCONFIRM = 'Confirm';
    const TRIGGER_FORMCOMPLETE = 'Complete';
    
    protected $method = "post";
    
    protected $action = null;

    protected $useConfirm = false;    
    /**
     *
     * @api
     * @var mixed $formManager 
     * @access private
     * @link
     */
    private $formManager = null;

    /**
     *
     * @api
     * @var mixed $form 
     * @access private
     * @link
     */
    private $form = null;

    protected $fieldset = [];

    /**
     * 
     * @api
     * @param mixed $fieldset
     * @return mixed $fieldset
     * @link
     */
    public function setFieldset ($fieldset)
    {
        return $this->fieldset = $fieldset;
    }

    /**
     * 
     * @api
     * @return mixed $fieldset
     * @link
     */
    public function getFieldset ()
    {
        return $this->fieldset;
    }
    
    public function __construct($config)
    {
        parent::__construct($config);
        $this->addEventListener(self::TRIGGER_INIT, function () {
            $form = $this->getFormManager()->create($this->getId());
            $this->setForm($form);
            $action = str_replace('//', '/', '/' . $this->getServiceManager()->getApplication()->getRouteModel()->getReq());
            $form->set('action', $action);
            $form->set('method', $this->getMethod());
            foreach($this->getFieldset() as $name => $fieldset) {
                $fieldset['name'] = $name;
                $form->addFieldset($fieldset);
            }
            $this->triggerEvent(self::TRIGGER_FORMINIT);
            $form->submit([$this, 'triggerForSubmit']);
            $confirm = false;
            if($this->useConfirm) {
                $confirm = [$this, 'triggerForConfirm'];
            }

            $form->confirm($confirm, [$this, 'triggerForComplete']);
        });
    }

    /**
     * 
     * @api
     * @param mixed $form
     * @return mixed $form
     * @link
     */
    public function setForm ($form)
    {
        return $this->form = $form;
    }

    /**
     * 
     * @api
     * @return mixed $form
     * @link
     */
    public function getForm ()
    {
        return $this->form;
    }

    /**
     * 
     * @api
     * @param mixed $formManager
     * @return mixed $formManager
     * @link
     */
    public function setFormManager ($formManager)
    {
        return $this->formManager = $formManager;
    }

    /**
     * 
     * @api
     * @return mixed $formManager
     * @link
     */
    public function getFormManager ()
    {
        if($this->formManager === null) {
            $this->formManager = new FormManager;
        }
        return $this->formManager;
    }

    public function triggerForSubmit($data)
    {
        $this->triggerEvent(self::TRIGGER_FORMSUBMIT, [$data]);
    }
    
    public function triggerForConfirm($data)
    {
        $this->triggerEvent(self::TRIGGER_FORMCONFIRM, [$data]);
    }

    public function triggerForComplete($data)
    {
        $this->triggerEvent(self::TRIGGER_FORMCOMPLETE, [$data]);
    }

    /**
     * 
     * @api
     * @param mixed $method
     * @return mixed $method
     * @link
     */
    public function setMethod ($method)
    {
        return $this->method = $method;
    }

    /**
     * 
     * @api
     * @return mixed $method
     * @link
     */
    public function getMethod ()
    {
        return $this->method;
    }
}
