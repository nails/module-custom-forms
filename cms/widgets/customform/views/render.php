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
$iFormId       = !empty($formId) ? (int) $formId: null;
$bShowLabel    = !empty($showLabel);
$bShowHeader   = !empty($showHeader);
$bShowFooter   = !empty($showFooter);
$bCaptchaError = !empty($captchaError);

if (!empty($iFormId)) {

    $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');
    $oForm      = $oFormModel->getById($iFormId, array('includeForm' => true));

} elseif (!empty($form)) {

    $oForm = $form;

} else {

    $bShowWidget = false;
}

if ($bShowWidget) {

    Factory::helper('formbuilder', 'nailsapp/module-form-builder');

    ?>
    <div class="cms-widget cms-widget-custom-forms">
        <?php

        if ($bShowLabel) {
            echo '<h2>' . $oForm->label . '</h2>';
        }

        if ($bShowHeader) {
            echo cmsAreaWithData($oForm->header);
        }

        //  Inject a captcha at the end of the fields, if required
        //  @todo
        /*
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

                get_instance()->load->view('formbuilder/fields/body-captcha', $aData);

            } elseif (nailsEnvironment('not', 'PRODUCTION')) {

                ?>
                <p class="alert alert-danger">
                    <strong>Failed to generate captcha</strong>
                    <br /><?=captchaError()?>
                </p>
                <?php
            }
        }
        */

        $aFormConfig = array(
            'form_action' => $oForm->url,
            'form_attr'   => $oForm->form_attributes,
            'fields'      => $oForm->form->fields->data,
            'buttons'     => array(
                array(
                    'label' => $oForm->cta->label,
                    'attr'  => $oForm->cta->attributes
                )
            )
        );
        echo formBuilderRender($aFormConfig);

        if ($bShowFooter) {
            echo cmsAreaWithData($oForm->footer);
        }

        ?>
    </div>
    <?php
}
