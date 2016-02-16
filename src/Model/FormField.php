<?php

/**
 * Manage form fields
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

class FormField extends Base
{
    protected $oDb;
    protected $aTypes;
    protected $aDefaults;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();

        $this->oDb               = Factory::service('Database');
        $this->table             = NAILS_DB_PREFIX . 'custom_form_field';
        $this->tablePrefix       = 'ff';
        $this->defaultSortColumn = 'order';

        // --------------------------------------------------------------------------

        //  Define additional types which can be set (in addition to TEXT and hidden)
        $this->aTypes = array(
            'TEXT' => (object) array(
                'model'    => 'FieldTypeText',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'PASSWORD' => (object) array(
                'model'    => 'FieldTypePassword',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'NUMBER' => (object) array(
                'model'    => 'FieldTypeNumber',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'EMAIL' => (object) array(
                'model'    => 'FieldTypeEmail',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'TEL' => (object) array(
                'model'    => 'FieldTypeTel',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'URL' => (object) array(
                'model'    => 'FieldTypeUrl',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'TEXTAREA' => (object) array(
                'model'    => 'FieldTypeTextarea',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'SELECT' => (object) array(
                'model'    => 'FieldTypeSelect',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'CHECKBOX' => (object) array(
                'model'    => 'FieldTypeCheckbox',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'RADIO' => (object) array(
                'model'    => 'FieldTypeRadio',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'DATE' => (object) array(
                'model'    => 'FieldTypeDate',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'TIME' => (object) array(
                'model'    => 'FieldTypeTime',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'DATETIME' => (object) array(
                'model'    => 'FieldTypeDateTime',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'FILE' => (object) array(
                'model'    => 'FieldTypeFile',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'HIDDEN' => (object) array(
                'model'    => 'FieldTypeHidden',
                'provider' => 'nailsapp/module-custom-forms'
            )
        );

        //  Set up the default values array
        $this->aDefaults = array(
            'NONE' => (object) array(
                'model'    => 'DefaultValueNone',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'USER_ID' => (object) array(
                'model'    => 'DefaultValueUserId',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'USER_NAME' => (object) array(
                'model'    => 'DefaultValueUserName',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'USER_FIRST_NAME' => (object) array(
                'model'    => 'DefaultValueUserFirstName',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'USER_LAST_NAME' => (object) array(
                'model'    => 'DefaultValueUserLastName',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'USER_EMAIL' => (object) array(
                'model'    => 'DefaultValueUserEmail',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'TIMESTAMP' => (object) array(
                'model'    => 'DefaultValueTimestamp',
                'provider' => 'nailsapp/module-custom-forms'
            ),
            'CUSTOM' => (object) array(
                'model'    => 'DefaultValueCustom',
                'provider' => 'nailsapp/module-custom-forms'
            ),
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all field objects
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
            $this->getManyAssociatedItems(
                $aItems,
                'options',
                'form_field_id',
                'FormFieldOption',
                'nailsapp/module-custom-forms'
            );
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

        $aIntegers[] = 'form_id';
        $aBools[]    = 'is_required';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        // --------------------------------------------------------------------------

        //  Work out the default value
        $oObj->default_value_processed = '';

        if ($oObj->default_value === 'CUSTOM') {

            $oObj->default_value_processed = $oObj->default_value_custom;

        } else {

            $aDefaults = $this->getDefaultValues();
            foreach ($aDefaults as $sKey => $oDefault) {
                if ($sKey == $oObj->default_value) {
                    $oObj->default_value_processed = $oDefault->instance->defaultValue();
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Return all the available types of field which can be created
     * @return array
     */
    public function getTypes()
    {
        foreach ($this->aTypes as $oType) {
            if (!isset($oType->instance)) {
                $oType->instance = Factory::model($oType->model, $oType->provider);
            }
        }

        return $this->aTypes;
    }

    // --------------------------------------------------------------------------

    /**
     * Get an individual field type instance by it's slug
     * @param  string $sSlug The Field Type's slug
     * @return object
     */
    public function getType($sSlug) {

        $aTypes = $this->getTypes();

        foreach ($aTypes as $sTypeSlug => $oType) {
            if ($sTypeSlug == $sSlug) {
                return $oType->instance;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Return all the available types of field which can be created as a flat array
     * @return array
     */
    public function getTypesFlat()
    {
        $aTypes = $this->getTypes();
        $aOut   = array();

        foreach ($aTypes as $sKey => $oType) {

            $oInstance   = $oType->instance;
            $aOut[$sKey] = $oInstance::LABEL;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the types which support defining multiple options
     * @return array
     */
    public function getTypesWithOptions()
    {
        $aTypes = $this->getTypes();
        $aOut   = array();

        foreach ($aTypes as $sKey => $oType) {

            $oInstance = $oType->instance;

            if ($oInstance::SUPPORTS_OPTIONS) {
                $aOut[] = $sKey;
            }
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the types which support a default value
     * @return array
     */
    public function getTypesWithDefaultValue()
    {
        $aTypes = $this->getTypes();
        $aOut   = array();

        foreach ($aTypes as $sKey => $oType) {

            $oInstance = $oType->instance;

            if ($oInstance::SUPPORTS_DEFAULTS) {
                $aOut[] = $sKey;
            }
        }

        return $aOut;
    }


    // --------------------------------------------------------------------------


    /**
     * Returns the various default values which a field can have
     * @return array
     */
    public function getDefaultValues()
    {
        foreach ($this->aDefaults as $oDefault) {
            if (!isset($oDefault->instance)) {
                $oDefault->instance = Factory::model($oDefault->model, $oDefault->provider);
            }
        }

        return $this->aDefaults;
    }

    // --------------------------------------------------------------------------

    /**
     * Get an individual default value instance by it's slug
     * @param  string $sSlug The Default Value's slug
     * @return object
     */
    public function getDefaultValue($sSlug) {

        $aDefaultValues = $this->getDefaultValues();

        foreach ($aDefaultValues as $sDefaultValueSlug => $oDefaultValue) {
            if ($sDefaultValueSlug == $sSlug) {
                return $oDefaultValue->instance;
            }
        }

        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the various default values which a field can have as a flat array
     * @return array
     */
    public function getDefaultValuesFlat()
    {
        $aDefaults = $this->getDefaultValues();
        $aOut      = array();

        foreach ($aDefaults as $sKey => $oDefault) {
            $oInstance   = $oDefault->instance;
            $aOut[$sKey] = $oInstance::LABEL;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a new field
     * @param array   $aData         The data to create the object with
     * @param boolean $bReturnObject Whether to return just the new ID or the full object
     * @return mixed
     */
    public function create($aData = array(), $bReturnObject = false)
    {
        $aOptions = array_key_exists('options', $aData) ? $aData['options'] : array();
        unset($aData['options']);

        try {

            $this->oDb->trans_begin();

            $mResult = parent::create($aData, $bReturnObject);

            if ($mResult) {

                $iFormFieldId = $bReturnObject ? $mResult->id : $mResult;

                if (!$this->saveAsscociatedItems($iFormFieldId, $aOptions, 'form_field_id', 'FormFieldOption', 'nailsapp/module-custom-forms')) {
                    throw new \Exception('Failed to update options.', 1);
                }

            } else {
                throw new \Exception('Failed to create field. ' . $this->lastError(), 1);
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
     * Update an existing field
     * @param int   $iId   The ID of the field to update
     * @param array $aData The data to update the field with
     * @return mixed
     */
    public function update($iId, $aData = array())
    {
        $aOptions = array_key_exists('options', $aData) ? $aData['options'] : array();
        unset($aData['options']);

        try {

            $this->oDb->trans_begin();

            if (parent::update($iId, $aData)) {
                if (!$this->saveAsscociatedItems($iId, $aOptions, 'form_field_id', 'FormFieldOption', 'nailsapp/module-custom-forms')) {
                    throw new \Exception('Failed to update field options.', 1);
                }
            } else {
                throw new \Exception('Failed to update field. ' . $this->lastError(), 1);
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
