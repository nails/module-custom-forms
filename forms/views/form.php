<div class="nails-custom-forms form">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h1 class="text-center">
                    <?=$oForm->label?>
                </h1>
                <?php

                if (!empty($oForm->header)) {
                    echo cmsAreaWithData($oForm->header);
                }

                $aFormConfig = array(
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

                if (!empty($oForm->footer)) {
                    echo cmsAreaWithData($oForm->footer);
                }

                ?>
            </div>
        </div>
    </div>
</div>