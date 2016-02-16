<?php

/**
 * This class provides the "File" field type
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\FieldType;

use Nails\Factory;

class File extends Base
{
    const LABEL = 'File';

    // --------------------------------------------------------------------------

    /**
     * Renders the field's HTML
     * @param  $aData The field's data
     * @return string
     */
    public function render($aData)
    {
        $sOut  = get_instance()->load->view('forms/fields/open', $aData);
        $sOut .= get_instance()->load->view('forms/fields/body-file', $aData);
        $sOut .= get_instance()->load->view('forms/fields/close', $aData);

        return $sOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Validate the user's input
     * @param  mixed    $mInput The form input's value
     * @param  stdClass $oField The complete field object
     * @return boolean|string   boolean true if valid, string with error if invalid
     */
    public function validate($mInput, $oField)
    {
        if (!isset($_FILES['field']['error'][$oField->id]) && $oField->is_required) {
            return 'This field is required.';
            throw new \Exception('This field is required.', 1);
        }

        if (isset($_FILES['field']['error'][$oField->id]) && $_FILES['field']['error'][$oField->id] !== UPLOAD_ERR_OK) {

            switch ($_FILES['field']['error'][$oField->id]) {

                case UPLOAD_ERR_INI_SIZE:

                    $maxFileSize = function_exists('ini_get') ? ini_get('upload_max_filesize') : null;

                    if (!is_null($maxFileSize)) {

                        $oCdn = Factory::service('Cdn', 'nailsapp/module-cdn');
                        $maxFileSize = $oCdn->returnBytes($maxFileSize);
                        $maxFileSize = $oCdn->formatBytes($maxFileSize);

                        $sError = 'The file exceeds the maximum size accepted by this server (which is ' . $maxFileSize . ').';

                    } else {

                        $sError = 'The file exceeds the maximum size accepted by this server.';
                    }
                    break;

                case UPLOAD_ERR_FORM_SIZE:

                    $sError = 'The file exceeds the maximum size accepted by this server.';
                    break;

                case UPLOAD_ERR_PARTIAL:

                    $sError = 'The file was only partially uploaded.';
                    break;

                case UPLOAD_ERR_NO_FILE:

                    $sError = 'No file was uploaded.';
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:

                    $sError = 'This server cannot accept uploads at this time.';
                    break;

                case UPLOAD_ERR_CANT_WRITE:

                    $sError = 'Failed to write uploaded file to disk, you can try again.';
                    break;

                case UPLOAD_ERR_EXTENSION:

                    $sError = 'The file failed to upload due to a server configuration.';
                    break;

                default:

                    $sError = 'The file failed to upload.';
                    break;
            }

            throw new \Exception($sError, 1);
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Clean the user's input into a string (or array of strings) suitable for humans browsing in admin
     * @param  mixed    $mInput The form input's value
     * @param  stdClass $oField The complete field object
     * @return mixed
     */
    public function clean($mInput, $oField)
    {
        $sPath = !empty($_FILES['field']['tmp_name'][$oField->id]) ? $_FILES['field']['tmp_name'][$oField->id] : null;
        $sName = !empty($_FILES['field']['name'][$oField->id]) ? $_FILES['field']['name'][$oField->id] : null;

        if (!empty($sPath)) {

            $oCdn = Factory::service('Cdn', 'nailsapp/module-cdn');
            $oResult = $oCdn->objectCreate(
                $sPath,
                'custom-forms-user-upload',
                array('filename_display' => $sName)
            );

            if ($oResult) {

                $sOut  = $sName . ' &mdash; ';
                $sOut .=  anchor(cdnServe($oResult->id, true), 'Download File', 'class="btn btn-primary btn-xs"');
                return $sOut;

            } else {
                throw new \Exception('Failed to upload file. ' . $oCdn->lastError(), 1);
            }

        } else {

            return null;
        }
    }
}
