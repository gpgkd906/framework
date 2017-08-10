<?php
declare(strict_types=1);

namespace Framework\ViewModel;

use Framework\ViewModel\FormViewModel;

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
    public function setFieldset($fieldset)
    {
        return $this->fieldset = $fieldset;
    }

    /**
     *
     * @api
     * @return mixed $fieldset
     * @link
     */
    public function getFieldset()
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
     *
     * @api
     * @return mixed $form
     * @link
     */
    public function getForm()
    {
        return $this->form;
    }

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
