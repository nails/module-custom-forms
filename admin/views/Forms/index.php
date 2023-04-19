<?php

use Nails\Admin\Helper;

?>
<div class="group-custom-forms browse">
    <?=Helper::loadSearch($search)?>
    <?=Helper::loadPagination($pagination)?>
    <table class="table table-striped table-hover table-bordered table-responsive">
        <thead class="table-dark">
            <tr>
                <th class="id">ID</th>
                <th class="label">Label</th>
                <th class="datetime">Modified</th>
                <th class="user">Modified By</th>
                <th class="actions">Actions</th>
            </tr>
        </thead>
        <tbody class="align-middle">
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
                        <?=Helper::loadDatetimeCell($form->modified)?>
                        <?=Helper::loadUserCell($form->modified_by)?>
                        <td class="actions">
                            <?php

                            echo anchor($form->url, 'View', 'class="btn btn-xs btn-default" target="_blank"');

                            if (userHasPermission(\Nails\CustomForms\Admin\Permission\Form\Edit::class)) {
                                echo anchor(
                                    \Nails\CustomForms\Admin\Controller\Forms::url('edit/' . $form->id),
                                    'Edit',
                                    'class="btn btn-xs btn-primary"'
                                );
                            }

                            if (userHasPermission(\Nails\CustomForms\Admin\Permission\Response\Browse::class)) {
                                echo anchor(
                                    \Nails\CustomForms\Admin\Controller\Forms::url('responses/' . $form->id),
                                    'View Responses (' . number_format($form->responses) . ')',
                                    'class="btn btn-xs btn-warning"'
                                );
                            }

                            if (userHasPermission(\Nails\CustomForms\Admin\Permission\Form\Delete::class)) {
                                echo anchor(
                                    \Nails\CustomForms\Admin\Controller\Forms::url('delete/' . $form->id),
                                    'Delete',
                                    'class="btn btn-xs btn-danger confirm" data-body="This action is also not undoable." data-title="Confirm Delete"'
                                );
                            }

                            if (userHasPermission(\Nails\CustomForms\Admin\Permission\Form\Create::class)) {
                                echo anchor(
                                    \Nails\CustomForms\Admin\Controller\Forms::url('copy/' . $form->id),
                                    'Duplicate',
                                    'class="btn btn-xs btn-default confirm" data-body="This will create a copy of the form, excluding any existing responses."'
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
    <?=Helper::loadPagination($pagination)?>
</div>
