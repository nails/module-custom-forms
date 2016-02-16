<?php

/**
 * This class provides the "UserLastName" default value
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\DefaultValue;

class UserLastName extends Base
{
    const LABEL = 'User\'s Surname';

    // --------------------------------------------------------------------------

    /**
     * Return the calculated default value
     * @return mixed
     */
    public function defaultValue()
    {
        return activeUser('last_name');
    }
}
