<?php
declare(strict_types=1);

namespace Project\Core\AdminUser\Fieldset;

use Std\FormManager\Fieldset;

/**
 * AdminUser Fieldset
 */
class AdminUserForEditFieldset extends AdminUserFieldset
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function getDefaultFieldset()
    {
        $fieldset = parent::getDefaultFieldset();
        $fieldset['password']['inputSpecification'] = null;
        $fieldset['passwordConfirm']['inputSpecification'] = null;
        return $fieldset;
    }
}
