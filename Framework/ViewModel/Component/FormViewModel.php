<?php

namespace Framework\ViewModel\Component;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\Lib\Form\FormManager;
use Framework\Lib\Form\Validator;

class FormViewModel extends AbstractViewModel
{
    protected $id = "formView";
    
    protected $csrf = null;

    protected $method = "post";
    
    protected $action = null;
    
    protected $elements = [
        "email" => [
            'type' => 'text',
            'validator' => [
                [Validator::Exists, "required"]
            ]
        ],
        "password" => [
            'type' => 'password',
            'validator' => [
                [Validator::Exists, "required"],
                [Validator::Password, 'required']
            ]
        ],
        "passwordConfirm" => [
            'type' => 'password',
            'validator' => [
                [Validator::Exists, "required"],
                [Validator::Password, 'required']
            ]
        ]
    ];
}
