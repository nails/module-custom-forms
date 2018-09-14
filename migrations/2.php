<?php

/**
 * Migration:   2
 * Started:     18/04/2016
 * Finalised:   20/04/2016
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nails\ModuleCustomForms;

use Nails\Common\Console\Migrate\Base;

class Migration2 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD `slug` VARCHAR(150)  NULL  DEFAULT NULL  AFTER `id`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD `is_minimal` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `thankyou_page_body`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD `is_deleted` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `is_minimal`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD `form_id` INT(11)  UNSIGNED  NOT NULL  AFTER `slug`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD FOREIGN KEY (`form_id`) REFERENCES `{{NAILS_DB_PREFIX}}formbuilder_form` (`id`) ON DELETE RESTRICT;");
        $this->query("DROP TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option`;");
        $this->query("DROP TABLE `{{NAILS_DB_PREFIX}}custom_form_field`;");



    }
}
