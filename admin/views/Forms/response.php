<div class="group-custom-forms responses single">
    <table class="table table-striped table-hover table-bordered table-responsive">
        <thead class="table-dark">
            <tr>
                <th class="col-xs-4" colspan="2">
                    Question
                </th>
                <th class="col-xs-8">
                    Answer
                </th>
            </tr>
        </thead>
        <tbody>
            <?php

            if ($response->answers) {
                $i = 1;
                foreach ($response->answers as $oAnswer) {
                    ?>
                    <tr>
                        <td class="number">
                            <?=$i?>
                        </td>
                        <td class="question">
                            <?=$oAnswer->question?>
                        </td>
                        <td class="answer">
                            <?php

                            if (is_array($oAnswer->answer)) {

                                echo implode('<br />', $oAnswer->answer);

                            } elseif (!empty($oAnswer->field->type)) {

                                $sClass = $oAnswer->field->type;
                                $oField = new $sClass();

                                echo $oField->extractText(
                                    (string) $oAnswer->answer,
                                    $oAnswer->answer
                                );

                            } else {
                                echo $oAnswer->answer;
                            }

                            ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
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
