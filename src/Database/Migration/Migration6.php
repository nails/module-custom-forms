<?php

/**
 * Migration: 6
 * Started:   11/08/2022
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Database Migration
 * @author      Nails Dev Team
 */

namespace Nails\CustomForms\Database\Migration;

use Nails\Common\Interfaces;
use Nails\Common\Traits;
use Nails\CustomForms\Admin\Permission;

/**
 * Class Migration6
 *
 * @package Nails\Elasticsearch\Database\Migration
 */
class Migration6 implements Interfaces\Database\Migration
{
    use Traits\Database\Migration;

    // --------------------------------------------------------------------------

    const MAP = [
        'admin:forms:forms:browse'           => Permission\Form\Browse::class,
        'admin:forms:forms:create'           => Permission\Form\Create::class,
        'admin:forms:forms:edit'             => Permission\Form\Edit::class,
        'admin:forms:forms:delete'           => Permission\Form\Delete::class,
        'admin:forms:forms:responses'        => Permission\Response\Browse::class,
        'admin:forms:forms:responses_delete' => Permission\Response\Delete::class,
    ];

    // --------------------------------------------------------------------------

    /**
     * Execute the migration
     */
    public function execute(): void
    {
        //  On a fresh build, this table might not yet exist
        $oResult = $this->query('SHOW TABLES LIKE "{{NAILS_DB_PREFIX}}user_group"');
        if ($oResult->rowCount() === 0) {
            return;
        }

        $oResult = $this->query('SELECT id, acl FROM `{{NAILS_DB_PREFIX}}user_group`');
        while ($row = $oResult->fetchObject()) {

            $acl = json_decode($row->acl) ?? [];

            foreach ($acl as &$old) {
                $old = self::MAP[$old] ?? $old;
            }

            $acl = array_filter($acl);
            $acl = array_unique($acl);
            $acl = array_values($acl);

            $this
                ->prepare('UPDATE `{{NAILS_DB_PREFIX}}user_group` SET `acl` = :acl WHERE `id` = :id')
                ->execute([
                    ':id'  => $row->id,
                    ':acl' => json_encode($acl),
                ]);
        }
    }
}
