<p>
    The <strong>{{label}}</strong> form has just been submitted.
</p>
<hr />
<table class="default-style">
    <tbody>
    <?php

    foreach ($emailObject->data->answers as $oAnswer) {

        ?>
        <tr>
            <td class="left-header-cell">
                <?=$oAnswer->question?>
            </td>
            <td>
                <?php

                if (is_array($oAnswer->answer)) {

                    foreach ($oAnswer->answer as $sAnswer) {
                        echo $sAnswer;
                    }

                } else {

                    echo $oAnswer->answer;
                }

                ?>
            </td>
        </tr>
        <?php
    }

    ?>
    </tbody>
</table>