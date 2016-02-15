<div class="nails-custom-form">
    <?php

    echo cmsWidget(
        'customform',
        array(
            'formId'     => $oForm->id,
            'showLabel'  => false,
            'showHeader' => true,
            'showFooter' => true
        )
    );

    ?>
</div>