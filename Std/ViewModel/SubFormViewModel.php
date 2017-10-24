<?php
/**
 * PHP version 7
 * File SubFormViewModel.php
 * 
 * @category Module
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\ViewModel;

use Std\ViewModel\FormViewModel;

/**
 * Class SubFormViewModel
 * 
 * @category Class
 * @package  Std\ViewModel
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class SubFormViewModel extends AbstractViewModel implements FormViewModelInterface
{
    protected $fieldset = [];
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
     * Method setForm
     *
     * @param Form $form Form
     * 
     * @return this
     */
    public function setForm($form)
    {
        foreach ($this->getFieldset() as $fieldsetName => $fieldset) {
            if (is_array($fieldset)) {
                $fieldset = [
                    'name' => $fieldsetName,
                    'fieldset' => $fieldset,
                ];
            }
            $form->addFieldset($fieldset);
        }
        return $this->form = $form;
    }

    /**
     * Method getForm
     *
     * @return Form $form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Method setExportView
     *
     * @param ViewModel $exportView ExportViewModel
     * @return void
     */
    public function setExportView($exportView)
    {
        if ($exportView instanceof FormViewModelInterface) {
            $exportView->addEventListener(FormViewModel::TRIGGER_FORMINIT, function ($event) {
                $exportView = $event->getTarget();
                $this->setForm($exportView->getForm());
                $this->triggerEvent(FormViewModel::TRIGGER_FORMINIT);
            });
        }
        return parent::setExportView($exportView);
    }
}
