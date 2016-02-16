<?php

$sRequired = !empty($required) ? '*' : '';

?>
<div class="form-group <?=!empty($error) ? 'has-error' : ''?>">
    <label for="<?=$id?>">
        <?=$label . $sRequired?>
    </label>
    <?=$sub_label ? '<p class="sub-label">' . $sub_label . '</p>' : ''?>