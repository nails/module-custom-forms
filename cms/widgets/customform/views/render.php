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

use Nails\CustomForms\Constants;
use Nails\Factory;

$bShowWidget   = true;
$iFormId       = !empty($formId) ? (int) $formId : null;
$bShowLabel    = !empty($showLabel);
$bShowHeader   = !empty($showHeader);
$bShowFooter   = !empty($showFooter);
$bCaptchaError = !empty($captchaError);

if (!empty($iFormId)) {

    $oFormModel = Factory::model('Form', Constants::MODULE_SLUG);
    $oForm      = $oFormModel->getById(
        $iFormId,
        [
            'expand' => [
                [
                    'form',
                    [
                        'expand' => [
                            [
                                'fields',
                                ['expand' => ['options']],
                            ],
                        ],
                    ],
                ],
            ],
        ]
    );

} elseif (!empty($form)) {

    $oForm = $form;

} else {

    $bShowWidget = false;
}

if ($bShowWidget) {

    Factory::helper('formbuilder', 'nails/module-form-builder');

    ?>
    <div class="cms-widget cms-widget-custom-forms">
        <?php

        if ($bShowLabel) {
            echo '<h2>' . $oForm->label . '</h2>';
        }

        if ($bShowHeader) {
            echo cmsAreaWithData($oForm->header);
        }

        $aFormConfig = [
            'form_action' => $oForm->url,
            'form_attr'   => $oForm->form_attributes,
            'has_captcha' => $oForm->form->has_captcha,
            'fields'      => $oForm->form->fields->data,
            'buttons'     => [
                [
                    'label' => $oForm->cta->label,
                    'attr'  => $oForm->cta->attributes,
                ],
            ],
        ];
        echo formBuilderRender($aFormConfig);

        if ($bShowFooter) {
            echo cmsAreaWithData($oForm->footer);
        }

        ?>
    </div>
    <?php
}
