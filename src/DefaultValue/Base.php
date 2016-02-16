<?php

/**
 * This class provides a base for the different default values available
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\DefaultValue;

class Base
{
    const LABEL = '';

    // --------------------------------------------------------------------------

    /**
     * Return the calculated default value
     * @return mixed
     */
    public function defaultValue()
    {
        return null;
    }
}
