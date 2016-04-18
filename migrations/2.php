<?php

/**
 * Migration:   2
 * Started:     18/04/2016
 * Finalised:
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nailsapp\ModuleCustomForms;

use Nails\Common\Console\Migrate\Base;

class Migration2 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD `slug` VARCHAR(150)  NULL  DEFAULT NULL  AFTER `id`;
");
    }
}
