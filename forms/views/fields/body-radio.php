<?php

$aOptions = array();
foreach ($options as $oOption) {

    $sDisabled = $oOption->is_disabled ? 'disabled="disabled"' : '';

    if (!empty($value)) {

        $bSelected = $value == $oOption->id;

    } else {

        $bSelected = $oOption->is_selected;
    }

    ?>
    <div class="radio">
        <label>
            <?=form_radio($key, $oOption->id, $bSelected, $sDisabled)?>
            <?=$oOption->label?>
        </label>
    </div>
    <?php
}
