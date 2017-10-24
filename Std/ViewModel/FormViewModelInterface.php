<?php
/**
 * PHP version 7
 * File FormViewModelInterface.php
 * 
 * @category Module
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);
namespace Std\ViewModel;

/**
 * Interface FormViewModelInterface
 * 
 * @category Interface
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface FormViewModelInterface
{

    const TRIGGER_FORMINIT = 'forminit';
    const TRIGGER_FORMSUBMIT = 'Submit';
    const TRIGGER_FORMCONFIRM = 'Confirm';
    const TRIGGER_FORMCOMPLETE = 'Complete';

    /**
     * Method setFieldset
     *
     * @param array|string $fieldset FieldsetConfigOrFieldsetClass
     * 
     * @return mixed
     */
    public function setFieldset($fieldset);

    /**
     * Method getFieldset
     *
     * @return Fieldset $fieldset
     */
    public function getFieldset();

    /**
     * Method getForm
     *
     * @return Form $form
     */
    public function getForm();
}
