<?php

/**
 * This class provides the "Url" field type
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\FieldType;

use Nails\Factory;

class Url extends Base
{
    const LABEL             = 'URL';
    const SUPPORTS_DEFAULTS = true;
    const VALIDATION_RULES  = 'prep_url';

    // --------------------------------------------------------------------------

    /**
     * Renders the field's HTML
     * @param  $aData The field's data
     * @return string
     */
    public function render($aData)
    {
        $sOut  = get_instance()->load->view('forms/fields/open', $aData);
        $sOut .= get_instance()->load->view('forms/fields/body-url', $aData);
        $sOut .= get_instance()->load->view('forms/fields/close', $aData);

        return $sOut;
    }
}
