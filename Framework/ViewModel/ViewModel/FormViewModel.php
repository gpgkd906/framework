<?php

namespace Framework\ViewModel\ViewModel;

//use Framework\Lib\Form\FormManager;
//use Framework\Lib\Form\Validator;

class FormViewModel extends AbstractViewModel implements FormViewModelInterface
{
    protected $id = "formView";
    
    protected $csrf = null;

    protected $method = "post";
    
    protected $action = null;

    protected $fieldset = [
/*    
        "csrf" => [
            'type' => 'hidden',
            'validator' => [
                [Validator::Exists, "required"]
            ]
        ],
        'login' => [
            'type' => 'text',
            'validator' => [
                [Validator::Exists, "required"],
            ]
        ],
        'password' => [
            'type' => 'password',
            'validator' => [
                [Validator::Exists, "required"],
            ]
        ]
*/
    ];

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
    
    public $listeners = [
        'Render' => 'onRender',
    ];

    public function onRender()
    {
        var_dump("FormViewModel");
    }
}
