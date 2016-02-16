<?php

/**
 * This class provides the "Select" field type
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\FieldType;

use Nails\Factory;

class Select extends Base
{
    const LABEL            = 'Dropdown';
    const SUPPORTS_OPTIONS = true;

    // --------------------------------------------------------------------------

    /**
     * Renders the field's HTML
     * @param  $aData The field's data
     * @return string
     */
    public function render($aData)
    {
        $sOut  = get_instance()->load->view('forms/fields/open', $aData);
        $sOut .= get_instance()->load->view('forms/fields/body-select', $aData);
        $sOut .= get_instance()->load->view('forms/fields/close', $aData);

        return $sOut;
    }
}
