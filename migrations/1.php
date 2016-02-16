<?php

/**
 * Migration:   1
 * Started:     16/02/2016
 * Finalised:   16/02/2016
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nailsapp\ModuleCustomForms;

use Nails\Common\Console\Migrate\Base;

class Migration1 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD `cta_attributes` VARCHAR(255)  NOT NULL  DEFAULT ''  AFTER `cta_label`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` ADD `has_captcha` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `form_attributes`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` ADD `created` DATETIME  NOT NULL  AFTER `order`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` ADD `created_by` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `created`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` ADD `modified` DATETIME  NOT NULL  AFTER `created_by`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` ADD `modified_by` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `modified`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` ADD FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` ADD FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` CHANGE `type` `type` VARCHAR(50)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT 'TEXT';");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field` CHANGE `default_value` `default_value` VARCHAR(50)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT 'NONE';");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option` ADD `created` DATETIME  NOT NULL  AFTER `order`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option` ADD `created_by` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `created`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option` ADD `modified` DATETIME  NOT NULL  AFTER `created_by`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option` ADD `modified_by` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `modified`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option` ADD FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option` ADD FOREIGN KEY (`modified_by`) REFERENCES `advice` (`id`) ON UPDATE SET NULL;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_response` ADD `answers` TEXT  NULL  AFTER `form_id`;");
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_response` ADD `is_deleted` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0'  AFTER `answers`;");
        $this->query("DROP TABLE `{{NAILS_DB_PREFIX}}custom_form_response_answer`;");
    }
}
