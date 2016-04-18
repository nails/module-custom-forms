<?php

/**
 * Manage forms
 *
 * @package     Nails
 * @subpackage  module-form-builder
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\CustomForms\Model;

use Nails\Factory;
use Nails\Common\Model\Base;

class Form extends Base
{
    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();

        $this->table             = NAILS_DB_PREFIX . 'custom_form';
        $this->tablePrefix       = 'fbf';
        $this->destructiveDelete = false;
        $this->tableAutoSetSlugs = true;
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

            if (!empty($aData['includeAll']) || !empty($aData['includeForm'])) {
                $this->getSingleAssociatedItem(
                    $aItems,
                    'form_id',
                    'form',
                    'Form',
                    'nailsapp/module-form-builder',
                    array(
                        'includeFields' => true
                    )
                );
            }

            if (!empty($aData['includeAll']) || !empty($aData['includeResponses'])) {
                $this->getManyAssociatedItems(
                    $aItems,
                    'responses',
                    'form_id',
                    'Response',
                    'nailsapp/module-custom-forms'
                );
            }

            if (!empty($aData['includeAll']) || !empty($aData['countResponses'])) {
                $this->countManyAssociatedItems(
                    $aItems,
                    'responses_count',
                    'form_id',
                    'Response',
                    'nailsapp/module-custom-forms'
                );
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

        $oObj->url = site_url('forms/' . $oObj->slug);

        // --------------------------------------------------------------------------

        $oObj->header             = json_decode($oObj->header);
        $oObj->footer             = json_decode($oObj->footer);
        $oObj->notification_email = json_decode($oObj->notification_email);

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
        //  Extract the form
        $aForm = array_key_exists('form', $aData) ? $aData['form'] : null;
        unset($aData['form']);

        try {

            $oDb = Factory::service('Database');

            $oDb->trans_begin();

            //  Create the associated form (if no ID supplied)
            if (empty($aForm['id'])) {

                $oFormModel       = Factory::model('Form', 'nailsapp/module-form-builder');
                $aData['form_id'] = $oFormModel->create($aForm);

                if (!$aData['form_id']) {
                    throw new \Exception('Failed to create associated form.', 1);
                }

            } else {

                $aData['form_id'] = $aForm['id'];
            }

            $mResult = parent::create($aData, $bReturnObject);

            if (!$mResult) {
                throw new \Exception('Failed to create form. ' . $this->lastError(), 1);
            }

            $oDb->trans_commit();
            return $mResult;

        } catch (\Exception $e) {

            $oDb->trans_rollback();
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
        //  Extract the form
        $aForm = array_key_exists('form', $aData) ? $aData['form'] : null;
        unset($aData['form']);

        try {

            $oDb = Factory::service('Database');

            $oDb->trans_begin();

            //  Update the associated form (if no ID supplied)
            if (!empty($aForm['id'])) {

                $oFormModel = Factory::model('Form', 'nailsapp/module-form-builder');

                if (!$oFormModel->update($aForm['id'], $aForm)) {
                    throw new \Exception('Failed to update associated form.', 1);
                }
            }

            if (!parent::update($iId, $aData)) {
                throw new \Exception('Failed to update form. ' . $this->lastError(), 1);
            }

            $oDb->trans_commit();
            return true;

        } catch (\Exception $e) {

            $oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }
}
