<?php

/**
 * This class is the "Custom Forms" CMS editor view
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Widget
 * @author      Nails Dev Team
 * @link
 */

$oFormModel = \Nails\Factory::model('Form', 'nailsapp/module-custom-forms');
$aFormsFlat = $oFormModel->get_all_flat();

if (empty($aFormsFlat)) {

    ?>
    <div class="alert alert-warning">
        <strong>No Forms Available: </strong>Create some forms in the "Custom Forms" section of admin.
    </div>
    <?php

} else {

    ?>
    <div class="fieldset">
        <?php

        $aField            = array();
        $aField['key']     = 'formId';
        $aField['label']   = 'Form';
        $aField['class']   = 'select2';
        $aField['default'] = isset(${$aField['key']}) ? ${$aField['key']} : '';

        echo form_field_dropdown($aField, $aFormsFlat);

        ?>
    </div>
        <?php

}
