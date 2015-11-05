<div class="group-custom-forms browse">
    <p>
        Browse all custom forms.
    </p>
    <?=\Nails\Admin\Helper::loadSearch($search);?>
    <?=\Nails\Admin\Helper::loadPagination($pagination);?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="id">ID</th>
                    <th class="label">Label</th>
                    <th class="datetime">Created</th>
                    <th class="user">Created By</th>
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
                        <?=\Nails\Admin\Helper::loadDatetimeCell($form->created)?>
                        <?=\Nails\Admin\Helper::loadUserCell($form->created_by)?>
                        <td class="actions">
                        <?php

                        if (userHasPermission('admin:forms:forms:edit')) {

                            echo anchor('admin/forms/forms/edit/' . $form->id, 'Edit', 'class="btn btn-xs btn-primary"');
                        }

                        if (userHasPermission('admin:forms:forms:responses')) {

                            echo anchor(
                                'admin/forms/forms/responses/' . $form->id,
                                'View Responses (' . number_format($form->total_responses) . ')',
                                'class="btn btn-xs btn-warning"'
                            );
                        }

                        if (userHasPermission('admin:forms:forms:delete')) {

                            echo anchor(
                                'admin/forms/forms/delete/' . $form->id,
                                'Delete',
                                'class="confirm btn btn-xs btn-danger"'
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
    <?=\Nails\Admin\Helper::loadPagination($pagination)?>
</div>
