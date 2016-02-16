<?php

$aOptions = array();

foreach ($options as $oOption) {

    $sDisabled = $oOption->is_disabled ? 'disabled="disabled"' : '';

    if (!empty($value)) {

        $bSelected = in_array($oOption->id, $value);

    } else {

        $bSelected = $oOption->is_selected;
    }

    ?>
    <div class="checkbox">
        <label>
            <?=form_checkbox($key . '[]', $oOption->id, $bSelected, $sDisabled)?>
            <?=$oOption->label?>
        </label>
    </div>
    <?php
}
