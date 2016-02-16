<?php

/**
 * This class provides the "Email" field type
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\FieldType;

use Nails\Factory;

class Email extends Base
{
    const LABEL             = 'Email';
    const SUPPORTS_DEFAULTS = true;
    const VALIDATION_RULES  = 'valid_email';

    // --------------------------------------------------------------------------

    /**
     * Renders the field's HTML
     * @param  $aData The field's data
     * @return string
     */
    public function render($aData)
    {
        $sOut  = get_instance()->load->view('forms/fields/open', $aData);
        $sOut .= get_instance()->load->view('forms/fields/body-email', $aData);
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

        if (!valid_email($mInput)) {
            throw new \Exception('This field should be a valid email.', 1);
        }

        return true;
    }
}
