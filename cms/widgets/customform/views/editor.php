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

$oCi->load->model('forms/custom_form_model');
$aFormsFlat = $oCi->custom_form_model->get_all_flat();

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
