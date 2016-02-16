<?php

/**
 * This class provides the "Timestamp" default value
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\DefaultValue;

use Nails\Factory;

class Timestamp extends Base
{
    const LABEL = 'The current timestamp';

    // --------------------------------------------------------------------------

    /**
     * Return the calculated default value
     * @return mixed
     */
    public function defaultValue()
    {
        $oNow = Factory::factory('DateTime');
        return $oNow->format('Y-m-d\TH:i:s');
    }
}
