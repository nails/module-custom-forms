<?php

use Nails\Admin\Helper;

?>
<div class="group-custom-forms responses">
    <h2>Individual Responses (<?=number_format($form->responses->count)?>)</h2>
    <table class="table table-striped table-hover table-bordered table-responsive">
        <thead class="table-dark">
            <tr>
                <th class="usercell">
                    Submitted By
                </th>
                <th class="datetime">
                    Submitted On
                </th>
                <th class="actions">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($responses)) {
                foreach ($responses as $oResponse) {
                    ?>
                    <tr>
                        <?=Helper::loadUserCell($oResponse->created_by)?>
                        <?=Helper::loadDateTimeCell($oResponse->created)?>
                        <td class="actions">
                            <?php

                            echo anchor(
                                \Nails\CustomForms\Admin\Controller\Forms::url('responses/' . $form->id . '/' . $oResponse->id),
                                'View',
                                'class="btn btn-xs btn-primary"'
                            );

                            if (userHasPermission(\Nails\CustomForms\Admin\Permission\Response\Delete::class)) {
                                echo anchor(
                                    \Nails\CustomForms\Admin\Controller\Forms::url('responses/' . $form->id . '/' . $oResponse->id . '/delete'),
                                    'Delete',
                                    'class="btn btn-xs btn-danger confirm" data-body="This action is also not undoable." data-title="Confirm Delete"'
                                );
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
                        No Responses
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>
