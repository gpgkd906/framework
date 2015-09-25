<?php

namespace Framework\ViewModel;

use Framework\Core\ViewModel\AbstractViewModel;
use Framework\Lib\Form\FormManager;
use Framework\Lib\Form\Validator;

class FormViewModel extends AbstractViewModel
{
    private $id = "formView";
    
    private $csrf = null;

    private $method = "post";
    
    private $action = null;
    
    private $elements = [
        "email" => [
            'type' => 'text',
            'validator' => [
                [Validator::Exists, "required"]
            ]
        ],
        "password" => [
            'type' => 'password',
            'validator' => [
                [Validator::Exists, "required"]
                [Validator::Password, 'required']
            ]
        ],
        "passwordConfirm" => [
            'type' => 'password',
            'validator' => [
                [Validator::Exists, "required"]
                [Validator::Password, 'required']
            ]
        ]
    ];
}
