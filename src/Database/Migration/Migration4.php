<?php

/**
 * Migration:   4
 * Started:     10/10/2019
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Database\Migration;

use Nails\Common\Console\Migrate\Base;

class Migration4 extends Base
{
    /**
     * Execute the migration
     *
     * @return Void
     */
    public function execute()
    {
        $this->query('
            DROP TABLE IF EXISTS `{{NAILS_DB_PREFIX}}custom_form_notification`;
            CREATE TABLE `{{NAILS_DB_PREFIX}}custom_form_notification` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `form_id` int(11) unsigned NOT NULL,
                `email` varchar(150) NOT NULL DEFAULT \'\',
                `condition_enabled` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `condition_field_id` int(11) unsigned DEFAULT NULL,
                `condition_operator` enum(\'IS\',\'IS_NOT\',\'GREATER_THAN\',\'LESS_THAN\', \'CONTAINS\') DEFAULT \'IS\',
                `condition_value` varchar(150) DEFAULT NULL,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `form_id` (`form_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                KEY `condition_field_id` (`condition_field_id`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_notification_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `{{NAILS_DB_PREFIX}}custom_form` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_notification_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_notification_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}custom_form_notification_ibfk_4` FOREIGN KEY (`condition_field_id`) REFERENCES `{{NAILS_DB_PREFIX}}formbuilder_form_field` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');

        $oStatement = $this->query('
            SELECT
                `form_id`,
                `notification_email`,
                `created`,
                `created_by`,
                `modified`,
                `modified_by`
            FROM `{{NAILS_DB_PREFIX}}custom_form`
        ');

        while ($oRow = $oStatement->fetch(\PDO::FETCH_OBJ)) {

            $aEmails = json_decode($oRow->notification_email) ?? [];
            foreach ($aEmails as $sEmail) {
                if (empty($sEmail)) {
                    continue;
                }

                $this
                    ->prepare('
                        INSERT INTO `{{NAILS_DB_PREFIX}}custom_form_notification`
                            (`form_id`, `email`, `created`, `created_by`, `modified`, `modified_by`)
                        VALUES
                            (:form_id, :email, :created, :created_by, :modified, :modified_by);
                    ')
                    ->execute([
                        ':form_id'     => $oRow->form_id,
                        ':email'       => $sEmail,
                        ':created'     => $oRow->created,
                        ':created_by'  => $oRow->created_by,
                        ':modified'    => $oRow->modified,
                        ':modified_by' => $oRow->modified_by,
                    ]);
            }
        }

        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` DROP `notification_email`;');
    }
}
