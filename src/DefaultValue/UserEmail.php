<?php

/**
 * This class provides the "UserEmail" default value
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\DefaultValue;

class UserEmail extends Base
{
    const LABEL = 'User\'s Email';

    // --------------------------------------------------------------------------

    /**
     * Return the calculated default value
     * @return mixed
     */
    public function defaultValue()
    {
        return activeUser('email');
    }
}
