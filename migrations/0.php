<?php

/**
 * Migration:   0
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

class Migration0 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("CREATE TABLE `{{NAILS_DB_PREFIX}}custom_form` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `label` varchar(150) NOT NULL DEFAULT '', `header` text NOT NULL, `footer` text NOT NULL, `cta_label` varchar(100) NOT NULL DEFAULT '', `form_attributes` varchar(255) NOT NULL DEFAULT '', `notification_email` varchar(255) NOT NULL DEFAULT '', `thankyou_email` tinyint(1) unsigned NOT NULL DEFAULT '0', `thankyou_email_subject` varchar(150) NOT NULL DEFAULT '', `thankyou_email_body` text NOT NULL, `thankyou_page_title` varchar(150) NOT NULL DEFAULT '', `thankyou_page_body` text NOT NULL, `created` datetime NOT NULL, `created_by` int(11) unsigned DEFAULT NULL, `modified` datetime NOT NULL, `modified_by` int(11) unsigned DEFAULT NULL, PRIMARY KEY (`id`), KEY `created_by` (`created_by`), KEY `modified_by` (`modified_by`), CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL, CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->query("CREATE TABLE `{{NAILS_DB_PREFIX}}custom_form_field` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `form_id` int(11) unsigned NOT NULL, `type` enum('TEXT','NUMBER','EMAIL','TEL','TEXTAREA','SELECT','CHECKBOX','RADIO','DATE','TIME','DATETIME','HIDDEN') NOT NULL DEFAULT 'TEXT', `label` varchar(150) NOT NULL DEFAULT '', `sub_label` varchar(255) NOT NULL DEFAULT '', `placeholder` varchar(255) NOT NULL DEFAULT '', `is_required` tinyint(1) unsigned NOT NULL DEFAULT '0', `default_value` enum('USER_ID','BUSINESS_ID','BUSINESS_NAME','USER_NAME','USER_EMAIL','USER_TELEPHONE','CUSTOM','CURRENT_TIMESTAMP') DEFAULT NULL, `default_value_custom` varchar(255) NOT NULL DEFAULT '', `custom_attributes` text NOT NULL, `order` int(11) unsigned NOT NULL DEFAULT '0', PRIMARY KEY (`id`), KEY `form_id` (`form_id`), CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_field_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `{{NAILS_DB_PREFIX}}custom_form` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->query("CREATE TABLE `{{NAILS_DB_PREFIX}}custom_form_field_option` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `form_field_id` int(11) unsigned NOT NULL, `label` varchar(255) NOT NULL DEFAULT '', `is_disabled` tinyint(1) unsigned NOT NULL DEFAULT '0', `is_selected` tinyint(1) unsigned NOT NULL DEFAULT '0', `order` int(11) unsigned NOT NULL DEFAULT '0', PRIMARY KEY (`id`), KEY `form_item_id` (`form_field_id`), CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_field_option_ibfk_1` FOREIGN KEY (`form_field_id`) REFERENCES `{{NAILS_DB_PREFIX}}custom_form_field` (`id`) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->query("CREATE TABLE `{{NAILS_DB_PREFIX}}custom_form_response` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `form_id` int(11) unsigned NOT NULL, `created` datetime NOT NULL, `created_by` int(11) unsigned DEFAULT NULL, `modified` datetime NOT NULL, `modified_by` int(11) unsigned DEFAULT NULL, PRIMARY KEY (`id`), KEY `form_id` (`form_id`), KEY `created_by` (`created_by`), KEY `modified_by` (`modified_by`), CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_response_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `{{NAILS_DB_PREFIX}}custom_form` (`id`) ON DELETE CASCADE, CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_response_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL, CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_response_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $this->query("CREATE TABLE `{{NAILS_DB_PREFIX}}custom_form_response_answer` (`id` int(11) unsigned NOT NULL AUTO_INCREMENT, `form_response_id` int(11) unsigned NOT NULL, `form_field_id` int(11) unsigned NOT NULL, `form_field_option_id` int(11) unsigned DEFAULT NULL, `value` text, PRIMARY KEY (`id`), KEY `form_response_id` (`form_response_id`), KEY `form_field_id` (`form_field_id`), KEY `form_field_option_id` (`form_field_option_id`), CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_response_answer_ibfk_1` FOREIGN KEY (`form_response_id`) REFERENCES `{{NAILS_DB_PREFIX}}custom_form_response` (`id`) ON DELETE CASCADE, CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_response_answer_ibfk_2` FOREIGN KEY (`form_field_id`) REFERENCES `{{NAILS_DB_PREFIX}}custom_form_field` (`id`), CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_response_answer_ibfk_3` FOREIGN KEY (`form_field_option_id`) REFERENCES `{{NAILS_DB_PREFIX}}custom_form_field_option` (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }
}
