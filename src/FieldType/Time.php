<?php

/**
 * This class provides the "Time" field type
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\FieldType;

use Nails\Factory;

class Time extends Base
{
    const LABEL             = 'Time';
    const SUPPORTS_DEFAULTS = true;

    // --------------------------------------------------------------------------

    /**
     * Renders the field's HTML
     * @param  $aData The field's data
     * @return string
     */
    public function render($aData)
    {
        $sOut  = get_instance()->load->view('forms/fields/open', $aData);
        $sOut .= get_instance()->load->view('forms/fields/body-time', $aData);
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

        $oDate = new \DateTime($mInput);
        if (empty($oDate)) {
            throw new \Exception('This field should be a valid time.', 1);
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Clean the user's input
     * @param  mixed    $mInput The form input's value
     * @param  stdClass $oField The complete field object
     * @return mixed
     */
    public function clean($mInput, $oField)
    {
        $oDate = new \DateTime($mInput);
        if (empty($oDate)) {
            return null;
        }
        return $oDate->format('H:i:s');
    }
}
