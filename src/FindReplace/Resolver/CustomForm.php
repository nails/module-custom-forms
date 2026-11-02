<?php

namespace Nails\CustomForms\FindReplace\Resolver;

use Nails\Common\Service\Database;
use Nails\CustomForms\Admin\Controller;
use Nails\Factory;
use Nails\FindReplace\Factory\Action;
use Nails\FindReplace\Factory\Hit;
use Nails\FindReplace\Factory\Matched;
use Nails\FindReplace\Interfaces\Resolver;

/**
 * Forms and their notifications are both edited on the form's edit screen.
 */
class CustomForm implements Resolver
{
    /**
     * The engine passes every match in a table at once; a long IN () list
     * overflows CodeIgniter's where_in parser, so IDs are queried in chunks.
     */
    const IDS_PER_QUERY = 500;

    public function tables(): array
    {
        return [
            NAILS_DB_PREFIX . 'custom_form',
            NAILS_DB_PREFIX . 'custom_form_notification',
        ];
    }

    public function resolve(string $table, array $matches): array
    {
        $ids = array_values(array_unique(array_map(fn(Matched $match): string => $match->getId(), $matches)));

        if ($ids === []) {
            return [];
        }

        return $table === NAILS_DB_PREFIX . 'custom_form_notification'
            ? $this->notifications($table, $ids)
            : $this->forms($table, $ids);
    }

    /**
     * @param list<string> $ids
     *
     * @return array<string, Hit>
     */
    private function forms(string $table, array $ids): array
    {
        $editUrl = $this->editUrl();
        $hits = [];

        foreach ($this->rows($table, 'id, label', $ids) as $row) {
            $id = (string) $row->id;

            $hits[$id] = new Hit(
                $table,
                $id,
                entityLabel: 'Custom Form',
                entityId: $id,
                title: (string) $row->label ?: null,
                actions: [
                    new Action('Edit', $editUrl . '/' . $id),
                ],
            );
        }

        return $hits;
    }

    /**
     * @param list<string> $ids
     *
     * @return array<string, Hit>
     */
    private function notifications(string $table, array $ids): array
    {
        $notifications = $this->rows($table, 'id, form_id', $ids);
        $formIds = array_values(array_unique(array_map(
            fn(\stdClass $notification): string => (string) $notification->form_id,
            $notifications
        )));

        $forms = [];

        if ($formIds !== []) {
            foreach ($this->rows(NAILS_DB_PREFIX . 'custom_form', 'id, label', $formIds) as $form) {
                $forms[(string) $form->id] = (string) $form->label;
            }
        }

        $editUrl = $this->editUrl();
        $hits = [];

        foreach ($notifications as $notification) {
            $id = (string) $notification->id;
            $formId = (string) $notification->form_id;

            //  A notification is only reachable through its form.
            if (!array_key_exists($formId, $forms)) {
                continue;
            }

            $hits[$id] = new Hit(
                $table,
                $id,
                entityLabel: 'Custom Form Notification',
                entityId: $id,
                title: $forms[$formId] ?: 'Form #' . $formId,
                actions: [
                    new Action('Edit form', $editUrl . '/' . $formId),
                ],
            );
        }

        return $hits;
    }

    /**
     * @param list<string> $ids
     *
     * @return list<\stdClass>
     */
    protected function rows(string $table, string $columns, array $ids): array
    {
        /** @var Database $database */
        $database = Factory::service('Database');
        $rows = [];

        foreach (array_chunk($ids, static::IDS_PER_QUERY) as $chunk) {
            $database->select($columns);
            $database->where_in('id', $chunk);
            $rows = array_merge($rows, $database->get($table)->result());
        }

        return $rows;
    }

    protected function editUrl(): string
    {
        return Controller\Forms::url('edit');
    }
}
