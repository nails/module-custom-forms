<?php

/**
 * Manage forms
 *
 * @package     Nails
 * @subpackage  module-custom-forms
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Model;

use Nails\Factory;
use Nails\Common\Model\Base;

class Form extends Base
{
    private $oDb;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();

        $this->oDb         = Factory::service('Database');
        $this->table       = NAILS_DB_PREFIX . 'custom_form';
        $this->tablePrefix = 'f';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all form objects
     * @param null    $iPage            The page to return
     * @param null    $iPerPage         The number of objects per page
     * @param array   $aData            Data to pass to _getcount_common
     * @param boolean $bIncludeDeleted  Whether to include deleted results
     * @return array
     */
    public function getAll($iPage = null, $iPerPage = null, $aData = array(), $bIncludeDeleted = false)
    {
        $aItems = parent::getAll($iPage, $iPerPage, $aData, $bIncludeDeleted);

        if (!empty($aItems)) {

            if (!empty($aData['includeAll']) || !empty($aData['includeFields'])) {
                $this->getManyAssociatedItems($aItems, 'fields', 'form_id', 'FormField', 'nailsapp/module-custom-forms');
            }

            if (!empty($aData['includeAll']) || !empty($aData['includeResponses'])) {
                $this->getManyAssociatedItems($aItems, 'responses', 'form_id', 'Response', 'nailsapp/module-custom-forms');
            }
        }

        return $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $oObj      A reference to the object being formatted.
     * @param  array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param  array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param  array  $aBools    Fields which should be cast as booleans if not null
     * @param  array  $aFloats   Fields which should be cast as floats if not null
     * @return void
     */
    protected function formatObject(
        &$oObj,
        $aData = array(),
        $aIntegers = array(),
        $aBools = array(),
        $aFloats = array()
    ) {

        $aBools[] = 'has_captcha';
        $aBools[] = 'thankyou_email';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        // --------------------------------------------------------------------------

        $oObj->url = site_url('forms/' . $oObj->id);

        // --------------------------------------------------------------------------

        $oObj->header             = json_decode($oObj->header);
        $oObj->footer             = json_decode($oObj->footer);
        $oObj->notification_email = json_decode($oObj->notification_email);

        // --------------------------------------------------------------------------

        $oObj->form             = new \stdClass();
        $oObj->form->attributes = $oObj->form_attributes;

        unset($oObj->form_attributes);

        // --------------------------------------------------------------------------

        $oObj->cta             = new \stdClass();
        $oObj->cta->label      = $oObj->cta_label;
        $oObj->cta->attributes = $oObj->cta_attributes;

        unset($oObj->cta_label);
        unset($oObj->cta_attributes);

        // --------------------------------------------------------------------------

        $bSendThankYouEmail = $oObj->thankyou_email;

        $oObj->thankyou_email          = new \stdClass();
        $oObj->thankyou_email->send    = $bSendThankYouEmail;
        $oObj->thankyou_email->subject = $oObj->thankyou_email_subject;
        $oObj->thankyou_email->body    = $oObj->thankyou_email_body;

        unset($oObj->thankyou_email_subject);
        unset($oObj->thankyou_email_body);

        // --------------------------------------------------------------------------

        $oObj->thankyou_page        = new \stdClass();
        $oObj->thankyou_page->title = $oObj->thankyou_page_title;
        $oObj->thankyou_page->body  = $oObj->thankyou_page_body;

        unset($oObj->thankyou_page_title);
        unset($oObj->thankyou_page_body);
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a new form
     * @param array   $aData         The data to create the object with
     * @param boolean $bReturnObject Whether to return just the new ID or the full object
     * @return mixed
     */
    public function create($aData = array(), $bReturnObject = false)
    {
        $aFields = array_key_exists('fields', $aData) ? $aData['fields'] : array();
        unset($aData['fields']);

        try {

            $this->oDb->trans_begin();

            $mResult = parent::create($aData, $bReturnObject);

            if ($mResult) {

                $iFormId = $bReturnObject ? $mResult->id : $mResult;

                if (!$this->saveAsscociatedItems($iFormId, $aFields, 'form_id', 'FormField', 'nailsapp/module-custom-forms')) {
                    throw new \Exception('Failed to update fields.', 1);
                }

            } else {
                throw new \Exception('Failed to create form. ' . $this->lastError(), 1);
            }

            $this->oDb->trans_commit();
            return $mResult;

        } catch (\Exception $e) {

            $this->oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update an existing form
     * @param int   $iId   The ID of the form to update
     * @param array $aData The data to update the form with
     * @return mixed
     */
    public function update($iId, $aData = array())
    {
        $aFields = array_key_exists('fields', $aData) ? $aData['fields'] : array();
        unset($aData['fields']);

        try {

            $this->oDb->trans_begin();

            if (parent::update($iId, $aData)) {
                if (!$this->saveAsscociatedItems($iId, $aFields, 'form_id', 'FormField', 'nailsapp/module-custom-forms')) {
                    throw new \Exception('Failed to update fields.', 1);
                }
            } else {
                throw new \Exception('Failed to update form. ' . $this->lastError(), 1);
            }

            $this->oDb->trans_commit();
            return true;

        } catch (\Exception $e) {

            $this->oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }
}
