<div class="group-custom-forms responses single">
    <table>
        <thead>
            <tr>
                <th colspan="2">
                    Question
                </th>
                <th>
                    Answer
                </th>
            </tr>
        </thead>
        <tbody>
        <?php

        if (!empty($response->questions)) {

            foreach ($response->questions as $iIndex => $oQuestion) {

                ?>
                <tr>
                    <td class="number"><?=$oQuestion->number?></td>
                    <td class="question"><?=$oQuestion->question?></td>
                    <td class="answer">
                        <?php

                        if (is_array($oQuestion->answer)) {

                            echo implode('<br />', $oQuestion->answer);

                        } else {

                            echo $oQuestion->answer;
                        }

                        ?>
                    </td>
                </tr>
                <?php
            }


        } else {

            ?>
            <tr>
                <td colspan="3" class="no-data">
                    No Data
                </td>
            </tr>
            <?php
        }

        ?>
        </tbody>
    </table>
</div>
