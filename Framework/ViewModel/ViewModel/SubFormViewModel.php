<?php

namespace Framework\ViewModel\ViewModel;

class SubFormViewModel extends AbstractViewModel implements FormViewModelInterface
{
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
}