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

use Nails\Common\Model\Base;
use Nails\Factory;

class Form extends Base
{
    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'custom_form';
        $this->destructiveDelete = false;
        $this->tableAutoSetSlugs = true;
        $this->addExpandableField([
            'trigger'   => 'form',
            'type'      => self::EXPANDABLE_TYPE_SINGLE,
            'property'  => 'form',
            'model'     => 'Form',
            'provider'  => 'nailsapp/module-form-builder',
            'id_column' => 'form_id',
        ]);
        $this->addExpandableField([
            'trigger'   => 'responses',
            'type'      => self::EXPANDABLE_TYPE_MANY,
            'property'  => 'responses',
            'model'     => 'Response',
            'provider'  => 'nailsapp/module-custom-forms',
            'id_column' => 'form_id',
        ]);
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
     *
     * @return void
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
    ) {
        $aBools[] = 'thankyou_email';
        $aBools[] = 'is_minimal';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        // --------------------------------------------------------------------------

        $oObj->url = site_url('forms/' . $oObj->slug);

        // --------------------------------------------------------------------------

        $oObj->header             = json_decode($oObj->header);
        $oObj->footer             = json_decode($oObj->footer);
        $oObj->notification_email = json_decode($oObj->notification_email);

        // --------------------------------------------------------------------------

        $oObj->cta = (object) [
            'label'      => $oObj->cta_label,
            'attributes' => $oObj->cta_attributes,
        ];

        unset($oObj->cta_label);
        unset($oObj->cta_attributes);

        // --------------------------------------------------------------------------

        $bSendThankYouEmail   = $oObj->thankyou_email;
        $oObj->thankyou_email = (object) [
            'send'    => $bSendThankYouEmail,
            'subject' => $oObj->thankyou_email_subject,
            'body'    => $oObj->thankyou_email_body,
        ];

        unset($oObj->thankyou_email_subject);
        unset($oObj->thankyou_email_body);

        // --------------------------------------------------------------------------

        $oObj->thankyou_page = (object) [
            'title' => $oObj->thankyou_page_title,
            'body'  => json_decode($oObj->thankyou_page_body),
        ];

        unset($oObj->thankyou_page_title);
        unset($oObj->thankyou_page_body);
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a new form
     *
     * @param array   $aData         The data to create the object with
     * @param boolean $bReturnObject Whether to return just the new ID or the full object
     *
     * @return mixed
     */
    public function create(array $aData = [], $bReturnObject = false)
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
     *
     * @param int   $iId   The ID of the form to update
     * @param array $aData The data to update the form with
     *
     * @return mixed
     */
    public function update($iId, array $aData = [])
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
