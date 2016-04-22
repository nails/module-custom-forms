<?php

/**
 * Migration:   3
 * Started:     22/04/2016
 * Finalised:   22/04/2016
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nailsapp\ModulecustomForms;

use Nails\Common\Console\Migrate\Base;

class Migration3 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}survey_survey` DROP `has_captcha`;");
    }
}
