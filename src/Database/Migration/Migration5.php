<?php

/**
 * Migration:   5
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

class Migration5 extends Base
{
    /**
     * Execute the migration
     *
     * @return Void
     */
    public function execute()
    {
        $this->query('UPDATE `{{NAILS_DB_PREFIX}}custom_form` SET `header` = "[]" WHERE `header` = "";');
        $this->query('UPDATE `{{NAILS_DB_PREFIX}}custom_form` SET `footer` = "[]" WHERE `footer` = "";');
        $this->query('UPDATE `{{NAILS_DB_PREFIX}}custom_form` SET `thankyou_page_body` = "[]" WHERE `thankyou_page_body` = "";');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form_response` CHANGE `answers` `answers` JSON NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` CHANGE `header` `header` JSON NOT NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` CHANGE `footer` `footer` JSON NOT NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}custom_form` CHANGE `thankyou_page_body` `thankyou_page_body` JSON NOT NULL;');
    }
}
