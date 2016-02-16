<div class="nails-custom-form">
    <?php

    echo cmsWidget(
        'customform',
        array(
            'form'       => $oForm,
            'showLabel'  => false,
            'showHeader' => true,
            'showFooter' => true
        )
    );

    ?>
</div>