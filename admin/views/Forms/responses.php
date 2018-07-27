<div class="group-custom-forms responses">
    <h2>Individual Responses (<?=number_format($form->responses->count)?>)</h2>
    <table>
        <thead>
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
                        <?=adminHelper('loadUserCell', $oResponse->created_by)?>
                        <?=adminHelper('loadDateTimeCell', $oResponse->created)?>
                        <td class="actions">
                            <?php

                            echo anchor(
                                'admin/forms/forms/responses/' . $form->id . '/' . $oResponse->id,
                                'View',
                                'class="btn btn-xs btn-primary"'
                            );

                            if (userHasPermission('admin:forms:forms:browse')) {
                                echo anchor(
                                    'admin/forms/forms/responses/' . $form->id . '/' . $oResponse->id . '/delete',
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
