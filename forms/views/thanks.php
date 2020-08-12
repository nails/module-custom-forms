<div class="nails-custom-forms thanks">
    <?php

    if (!empty($oForm->thankyou_page->title)) {
        echo '<h1>' . $oForm->thankyou_page->title . '</h1>';
    }

    echo cmsAreaWithData($oForm->thankyou_page->body);

    ?>
</div>
