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

use Nails\Factory;

$oFormModel = Factory::model('Form', 'nails/module-custom-forms');
$aFormsFlat = $oFormModel->getAllFlat();

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

        $aField            = array();
        $aField['key']     = 'showLabel';
        $aField['label']   = 'Show Label';
        $aField['default'] = isset(${$aField['key']}) ? ${$aField['key']} : '';

        echo form_field_boolean($aField);

        $aField            = array();
        $aField['key']     = 'showHeader';
        $aField['label']   = 'Show Header';
        $aField['default'] = isset(${$aField['key']}) ? ${$aField['key']} : '';

        echo form_field_boolean($aField);

        $aField            = array();
        $aField['key']     = 'showFooter';
        $aField['label']   = 'Show Footer';
        $aField['default'] = isset(${$aField['key']}) ? ${$aField['key']} : '';

        echo form_field_boolean($aField);

        ?>
    </div>
        <?php

}
