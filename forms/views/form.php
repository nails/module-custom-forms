<div class="nails-custom-forms form">
    <?php

    if (!empty($oForm->header)) {
        echo cmsAreaWithData($oForm->header);
    }

    echo formBuilderRender([
        'form_attr'     => $oForm->form_attributes,
        'has_captcha'   => $oForm->form->has_captcha,
        'captcha_error' => !empty($captchaError) ? $captchaError : null,
        'fields'        => $oForm->form->fields->data,
        'buttons'       => [
            [
                'label' => $oForm->cta->label,
                'attr'  => $oForm->cta->attributes,
            ],
        ],
    ]);

    if (!empty($oForm->footer)) {
        echo cmsAreaWithData($oForm->footer);
    }

    ?>
</div>
