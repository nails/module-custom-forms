<?php

/**
 * This class provides the "Number" field type
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\FieldType;

use Nails\Factory;

class Number extends Base
{
    const LABEL             = 'Number';
    const SUPPORTS_DEFAULTS = true;
    const VALIDATION_RULES  = 'is_numeric';

    // --------------------------------------------------------------------------

    /**
     * Renders the field's HTML
     * @param  $aData The field's data
     * @return string
     */
    public function render($aData)
    {
        $sOut  = get_instance()->load->view('forms/fields/open', $aData);
        $sOut .= get_instance()->load->view('forms/fields/body-number', $aData);
        $sOut .= get_instance()->load->view('forms/fields/close', $aData);

        return $sOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Validate the user's entry
     * @param  mixed    $mInput The form input's value
     * @param  stdClass $oField The complete field object
     * @return boolean|string   boolean true if valid, string with error if invalid
     */
    public function validate($mInput, $oField)
    {
        $mResult = parent::validate($mInput, $oField);

        if ($mResult !== true) {
            return $mResult;
        }

        if (!is_numeric($mInput)) {
            throw new \Exception('This field should be numeric.', 1);
        }

        return true;
    }
}
