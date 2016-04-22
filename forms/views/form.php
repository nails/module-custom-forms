<div class="nails-custom-forms form">
    <?php

    if (!empty($oForm->header)) {
        echo cmsAreaWithData($oForm->header);
    }

    $aFormConfig = array(
        'form_attr'     => $oForm->form_attributes,
        'has_captcha'   => $oForm->form->has_captcha,
        'captcha_error' => !empty($captchaError) ? $captchaError : null,
        'fields'        => $oForm->form->fields->data,
        'buttons'       => array(
            array(
                'label' => $oForm->cta->label,
                'attr'  => $oForm->cta->attributes
            )
        )
    );
    echo formBuilderRender($aFormConfig);

    if (!empty($oForm->footer)) {
        echo cmsAreaWithData($oForm->footer);
    }

    ?>
</div>