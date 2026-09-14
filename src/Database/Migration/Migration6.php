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

use Nails\Admin\Traits\Database\Migration\PermissionMap;
use Nails\Common\Interfaces;
use Nails\Common\Traits;
use Nails\CustomForms\Admin\Permission;

/**
 * Class Migration6
 *
 * Repeatable because `feature/pre-new-admin` has no equivalent migration, so an app
 * arriving from that branch resumes above this number and would never run it.
 *
 * @package Nails\CustomForms\Database\Migration
 */
class Migration6 implements Interfaces\Database\Migration\Repeatable
{
    use Traits\Database\Migration;
    use PermissionMap;

    // --------------------------------------------------------------------------

    const MAP = [
        'admin:forms:forms:browse'           => Permission\Form\Browse::class,
        'admin:forms:forms:create'           => Permission\Form\Create::class,
        'admin:forms:forms:edit'             => Permission\Form\Edit::class,
        'admin:forms:forms:delete'           => Permission\Form\Delete::class,
        'admin:forms:forms:responses'        => Permission\Response\Browse::class,
        'admin:forms:forms:responses_delete' => Permission\Response\Delete::class,
    ];
}
