<?php

/**
 * This is the "Custom Forms" CMS widget view
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Widget
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;

$bShowWidget   = true;
$sUuid         = md5(microtime(true));
$iFormId       = !empty($formId) ? (int) $formId: null;
$bShowLabel    = !empty($showLabel);
$bShowHeader   = !empty($showHeader);
$bShowFooter   = !empty($showFooter);
$bCaptchaError = !empty($captchaError);

if (!empty($iFormId)) {

    $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');
    $oForm      = $oFormModel->getById($iFormId, array('includeFields' => true));

} elseif (!empty($form)) {

    $oForm = $form;

} else {

    $bShowWidget = false;
}

if ($bShowWidget) {

    $oFormFieldModel = Factory::model('FormField', 'nailsapp/module-custom-forms');

    ?>
    <div class="cms-widget cms-widget-custom-forms">
        <?php

        if ($bShowLabel) {
            echo '<h2>' . $oForm->label . '</h2>';
        }

        if ($bShowHeader) {
            echo cmsAreaWithData($oForm->header);
        }

        echo form_open_multipart('forms/' . $oForm->id, $oForm->form->attributes);

        $iCounter = 0;

        foreach ($oForm->fields->data as $oField) {

            $oFieldType = $oFormFieldModel->getType($oField->type);

            $sId   = 'custom-form-' . $sUuid . '-' . $iCounter;
            $aAttr = array(
                $sId ? 'id="' . $sId . '"' : '',
                $oField->placeholder ? 'placeholder="' . $oField->placeholder . '"' : '',
                $oField->is_required ? 'required="required"' : '',
                $oField->custom_attributes
            );

            if (!empty($oFieldType)) {
                echo $oFieldType->render(
                    array(
                        'id'          => $sId,
                        'key'         => 'field[' . $oField->id . ']',
                        'label'       => $oField->label,
                        'sub_label'   => $oField->sub_label,
                        'default'     => $oField->default_value_processed,
                        'value'       => isset($_POST['field'][$oField->id]) ? $_POST['field'][$oField->id] : $oField->default_value_processed,
                        'required'    => $oField->is_required,
                        'class'       => 'form-control',
                        'attributes'  => implode(' ', $aAttr),
                        'options'     => $oField->options->data,
                        'error'       => !empty($oField->error) ? $oField->error : null
                    )
                );
            }

            $iCounter++;
        }

        // --------------------------------------------------------------------------

        if ($oForm->has_captcha) {

            nailsFactory('helper', 'captcha', 'nailsapp/module-captcha');
            $oCaptcha = captchaGenerate();

            if (!empty($oCaptcha)) {

                $aData = array(
                    'uuid'      => $sUuid,
                    'label'     => $oCaptcha->label,
                    'sub_label' => !empty($oCaptcha->sub_label) ? $oCaptcha->sub_label : null,
                    'html'      => $oCaptcha->html,
                    'error'     => $bCaptchaError
                );

                get_instance()->load->view('forms/fields/body-captcha', $aData);

            }
        }

        ?>
        <p>
            <button type="submit" class="btn btn-primary" <?=$oForm->cta->attributes?>>
                <?=$oForm->cta->label ?: 'Submit'?>
            </button>
        </p>
        <?php

        echo form_close();

        if ($bShowFooter) {
            echo cmsAreaWithData($oForm->footer);
        }

        ?>
    </div>
    <?php
}
