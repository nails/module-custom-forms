The {{label}} form has just been submitted.

---

<?php

foreach ($emailObject->data->answers as $oAnswer) {

    echo '[' . $oAnswer->question . "]\n";
    if (is_array($oAnswer->answer)) {

        foreach ($oAnswer->answer as $sAnswer) {
            echo strip_tags($sAnswer) . "\n\n";
        }

    } elseif (!empty($oAnswer->field->type)) {

        $sClass = $oAnswer->field->type;
        $oField = new $sClass();

        echo $oField->extractText(
                $oAnswer->answer,
                $oAnswer->answer,
                true
            ) . "\n\n";

    } else {

        echo strip_tags($oAnswer->answer) . "\n\n";
    }
}
