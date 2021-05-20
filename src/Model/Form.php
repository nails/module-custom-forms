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

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Model\Base;
use Nails\Common\Service\Database;
use Nails\CustomForms\Constants;
use Nails\Factory;

/**
 * Class Form
 *
 * @package Nails\CustomForms\Model
 */
class Form extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'custom_form';

    /**
     * Whether to automatically set slugs or not
     *
     * @var bool
     */
    const AUTO_SET_SLUG = true;

    /**
     * Whether a slug is immutable or not
     *
     * @var bool
     */
    const AUTO_SET_SLUG_IMMUTABLE = false;

    /**
     * Whether this model uses destructive delete or not
     *
     * @var bool
     */
    const DESTRUCTIVE_DELETE = false;

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Form';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    // --------------------------------------------------------------------------

    /**
     * Form constructor.
     *
     * @throws ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this
            ->addExpandableField([
                'trigger'   => 'form',
                'model'     => 'Form',
                'provider'  => \Nails\FormBuilder\Constants::MODULE_SLUG,
                'id_column' => 'form_id',
            ])
            ->addExpandableField([
                'trigger'   => 'notifications',
                'type'      => self::EXPANDABLE_TYPE_MANY,
                'model'     => 'FormNotification',
                'provider'  => Constants::MODULE_SLUG,
                'id_column' => 'form_id',
            ])
            ->addExpandableField([
                'trigger'   => 'responses',
                'type'      => self::EXPANDABLE_TYPE_MANY,
                'model'     => 'Response',
                'provider'  => Constants::MODULE_SLUG,
                'id_column' => 'form_id',
            ]);
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
            $oDb->transaction()->start();

            //  Create the associated form (if no ID supplied)
            if (empty($aForm['id'])) {

                $oFormModel       = Factory::model('Form', \Nails\FormBuilder\Constants::MODULE_SLUG);
                $aData['form_id'] = $oFormModel->create($aForm);

                if (!$aData['form_id']) {
                    throw new NailsException('Failed to create associated form.');
                }

            } else {
                $aData['form_id'] = $aForm['id'];
            }

            $mResult = parent::create($aData, $bReturnObject);
            if (!$mResult) {
                throw new NailsException('Failed to create form. ' . $this->lastError());
            }

            $oDb->transaction()->commit();
            return $mResult;

        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Copies an existing Custom Form
     *
     * @param int   $iFormId       The ID of the form to copy
     * @param bool  $bReturnObject Whether to return the new form's ID, or object
     * @param array $aReturnData   If returning an object, data to pass to the getById method
     *
     * @return mixed|\Nails\Common\Resource|null
     * @throws ValidationException
     * @throws FactoryException
     * @throws ModelException
     */
    public function copy(int $iFormId, bool $bReturnObject = false, array $aReturnData = [])
    {
        $oForm = $this->getById($iFormId);
        if (empty($iFormId)) {
            throw new ValidationException('Invalid form ID');
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        /** @var \Nails\FormBuilder\Model\Form $oFormModel */
        $oFormModel = Factory::model('Form', \Nails\FormBuilder\Constants::MODULE_SLUG);
        /** @var \DateTime $oNow */
        $oNow = Factory::factory('DateTime');

        try {

            //  Duplicate the base form
            $iNewFormId = $oFormModel->copy($oForm->form_id);
            if (empty($iNewFormId)) {
                throw new ModelException(
                    'Failed to copy FormBuilder form. ' . $oFormModel->lastError()
                );
            }

            //  Duplicate the custom form
            $oBasicForm = $oDb->query('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ' . $oForm->id)
                ->row();

            //  Update some things
            unset($oBasicForm->id);
            unset($oBasicForm->form_id);
            unset($oBasicForm->created);
            unset($oBasicForm->created_by);
            unset($oBasicForm->modified);
            unset($oBasicForm->modified_by);

            $oBasicForm->slug    = $this->generateSlug((array) $oBasicForm);
            $oBasicForm->form_id = $iNewFormId;
            $oBasicForm->label   .= ' - Copy (' . toUserDatetime($oNow->format('Y-m-d H:i:s')) . ')';

            $iCopiedFormId = parent::create((array) $oBasicForm);
            if (empty($iCopiedFormId)) {
                throw new ModelException('Failed to duplicate form. ' . $this->lastError());
            }

            $oDb->transaction()->commit();

        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            throw $e;
        }

        return $bReturnObject ? $this->getById($iCopiedFormId, $aReturnData) : $iCopiedFormId;
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
    public function update($iId, array $aData = []): bool
    {
        //  Extract the form
        $aForm = array_key_exists('form', $aData) ? $aData['form'] : null;
        unset($aData['form']);

        try {

            $oDb = Factory::service('Database');

            $oDb->transaction()->start();

            //  Update the associated form (if no ID supplied)
            if (!empty($aForm['id'])) {
                $oFormModel = Factory::model('Form', \Nails\FormBuilder\Constants::MODULE_SLUG);
                if (!$oFormModel->update($aForm['id'], $aForm)) {
                    throw new NailsException('Failed to update associated form.');
                }
            }

            if (!parent::update($iId, $aData)) {
                throw new NailsException('Failed to update form. ' . $this->lastError());
            }

            $oDb->transaction()->commit();
            return true;

        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param object $oObj      A reference to the object being formatted.
     * @param array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param array  $aBools    Fields which should be cast as booleans if not null
     * @param array  $aFloats   Fields which should be cast as floats if not null
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

        $oObj->url = siteUrl('forms/' . $oObj->slug);

        // --------------------------------------------------------------------------

        $oObj->header = json_decode($oObj->header);
        $oObj->footer = json_decode($oObj->footer);

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
}
