<div class="group-custom-forms browse">
    <p>
        Browse all custom forms.
    </p>
    <?=adminHelper('loadSearch', $search);?>
    <?=adminHelper('loadPagination', $pagination);?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="id">ID</th>
                    <th class="label">Label</th>
                    <th class="datetime">Modified</th>
                    <th class="user">Modified By</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php

            if ($forms) {

                foreach ($forms as $form) {

                    ?>
                    <tr>
                        <td class="id">
                            <?=number_format($form->id)?>
                        </td>
                        <td class="label">
                            <?=$form->label?>
                        </td>
                        <?=adminHelper('loadDatetimeCell', $form->modified)?>
                        <?=adminHelper('loadUserCell', $form->modified_by)?>
                        <td class="actions">
                        <?php

                        echo anchor($form->url, 'View', 'class="btn btn-xs btn-default" target="_blank"');

                        if (userHasPermission('admin:forms:forms:edit')) {

                            echo anchor('admin/forms/forms/edit/' . $form->id, 'Edit', 'class="btn btn-xs btn-primary"');
                        }

                        if (userHasPermission('admin:forms:forms:responses')) {

                            echo anchor(
                                'admin/forms/forms/responses/' . $form->id,
                                'View Responses (' . number_format($form->responses_count) . ')',
                                'class="btn btn-xs btn-warning"'
                            );
                        }

                        if (userHasPermission('admin:forms:forms:delete')) {

                            echo anchor(
                                'admin/forms/forms/delete/' . $form->id,
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
                    <td colspan="5" class="no-data">
                        No Forms Found
                    </td>
                </tr>
                <?php

            }

            ?>
            </tbody>
        </table>
    </div>
    <?=adminHelper('loadPagination', $pagination)?>
</div>
