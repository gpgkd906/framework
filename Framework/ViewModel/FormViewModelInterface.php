<?php
declare(strict_types=1);
namespace Framework\ViewModel;

interface FormViewModelInterface {

    const TRIGGER_FORMINIT = 'forminit';
    const TRIGGER_FORMSUBMIT = 'Submit';
    const TRIGGER_FORMCONFIRM = 'Confirm';
    const TRIGGER_FORMCOMPLETE = 'Complete';
    
    public function setFieldset($fieldset);

    public function getFieldset();

    public function getForm();
}
