<?php

/**
 * Manage Custom forms
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
    private $tableFields;
    private $tableFieldsPrefix;
    private $tableOptions;
    private $tableOptionsPrefix;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();

        $this->oDb                = Factory::service('Database');
        $this->table              = NAILS_DB_PREFIX . 'custom_form';
        $this->tablePrefix        = 'f';
        $this->tableFields        = NAILS_DB_PREFIX . 'custom_form_field';
        $this->tableFieldsPrefix  = 'ff';
        $this->tableOptions       = NAILS_DB_PREFIX . 'custom_form_field_option';
        $this->tableOptionsPrefix = 'ffo';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all form objects
     * @param null $page The page to return
     * @param null $perPage The number of objects per page
     * @param array $data Data to pass to _getcount_common
     * @param bool|false $includeDeleted Whether to include deleted results
     * @return array
     */
    public function get_all($page = null, $perPage = null, $data = array(), $includeDeleted = false) {

        $aForms = parent::get_all($page, $perPage, $data, $includeDeleted);

        //  @todo: do this in a more query efficient way: copy module-email-drip/Model/Campaign
        if (!empty($aForms)) {
            if (!empty($data['include_fields'])) {
                foreach ($aForms as $oForm) {
                    $oForm->fields = $this->getFieldsForForm($oForm->id);
                }
            }
        }

        return $aForms;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a form by its ID
     * @param int $iId The Id of the form to return
     * @param array $aData Data to pass to _getcount_common
     * @return mixed
     */
    public function get_by_id($iId, $aData = array())
    {
        $aData['include_fields'] = true;
        return parent::get_by_id($iId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param  array $data Data passed from the calling method
     * @return void
     **/
    protected function _getcount_common($aData = array())
    {
        $this->oDb->select('*');
        $this->oDb->select('
            (
                SELECT
                    COUNT(*)
                FROM ' . NAILS_DB_PREFIX . 'custom_form_response
                WHERE form_id = ' . $this->tablePrefix . '.id
            ) total_responses
        ');

        parent::_getcount_common($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all fields associated with a particular form
     * @param int $iFormId The ID of the form
     * @return mixed
     */
    private function getFieldsForForm($iFormId)
    {
        $this->oDb->select('id,type,label,sub_label,,placeholder,is_required');
        $this->oDb->select('default_value,default_value_custom,custom_attributes,order');
        $this->oDb->where('form_id', $iFormId);
        $this->oDb->order_by('order,id');
        $aFields = $this->oDb->get($this->tableFields)->result();

        if (!empty($aFields)) {
            foreach ($aFields as $oField) {
                switch ($oField->type) {

                    case 'SELECT' :
                    case 'RADIO' :
                    case 'CHECKBOX' :

                        $oField->options = $this->getOptionsForField($oField->id);
                        break;
                }

                $this->_format_field($oField);
            }
        }

        return $aFields;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all options for a particular field
     * @param int $iFieldId the field ID
     * @return mixed
     */
    private function getOptionsForField($iFieldId)
    {
        $this->oDb->select('id,label,is_disabled,is_selected');
        $this->oDb->where('form_field_id', $iFieldId);
        $this->oDb->order_by('order,id');
        $aOptions = $this->oDb->get($this->tableOptions)->result();

        if (!empty($aOptions)) {
            foreach ($aOptions as $oOption) {
                $this->_format_option($oOption);
            }
        }

        return $aOptions;
    }

    // --------------------------------------------------------------------------

    /**
     * Format a form object
     * @param object $obj The object to format
     * @param array $data Data passed to the calling get_* function
     * @param array $integers Fields to cast as integers
     * @param array $bools Fields to cast as booleans
     * @param array $floats Fields to cast as floats
     */
    protected function _format_object(&$obj, $data = array(), $integers = array(), $bools = array(), $floats = array())
    {
        $bools[] = 'is_external';
        parent::_format_object($obj, $data, $integers, $bools, $floats);
        $obj->notification_email = json_decode($obj->notification_email);
    }

    // --------------------------------------------------------------------------

    /**
     * Format a field object
     * @param object $obj The object to format
     * @param array $data Data passed to the calling get_* function
     * @param array $integers Fields to cast as integers
     * @param array $bools Fields to cast as booleans
     * @param array $floats Fields to cast as floats
     */
    protected function _format_field(&$obj, $data = array(), $integers = array(), $bools = array(), $floats = array())
    {
        $integers[] = 'form_id';
        $bools[] = 'is_required';
        parent::_format_object($obj, $data, $integers, $bools, $floats);
    }

    // --------------------------------------------------------------------------

    /**
     * Format an option object
     * @param object $obj The object to format
     * @param array $data Data passed to the calling get_* function
     * @param array $integers Fields to cast as integers
     * @param array $bools Fields to cast as booleans
     * @param array $floats Fields to cast as floats
     */
    protected function _format_option(&$obj, $data = array(), $integers = array(), $bools = array(), $floats = array())
    {
        $bools[] = 'is_disabled';
        $bools[] = 'is_selected';
        parent::_format_object($obj, $data, $integers, $bools, $floats);
    }

    // --------------------------------------------------------------------------

    /**
     * Creates a new form
     * @param array $aData The data to create the object with
     * @param boolean $bReturnObject Whether to return just the new ID or the full object
     * @return mixed
     */
    public function create($aData = array(), $bReturnObject = false)
    {
        $aFields = array_key_exists('fields', $aData) ? $aData['fields'] : array();
        unset($aData['fields']);

        $this->oDb->trans_begin();
        $mResult = parent::create($aData, $bReturnObject);

        if ($mResult) {

            $iFormId = $bReturnObject ? $mResult->id : $mResult;

            if ($this->updateFields($iFormId, $aFields)) {

                $this->oDb->trans_commit();
                return $mResult;

            } else {

                $this->setError('Failed to add fields.');
                $this->oDb->trans_rollback();
                return false;
            }

        } else {

            $this->oDb->trans_rollback();
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update an existing form
     * @param int $iId The ID of the form to update
     * @param array $aData The data to update the form with
     * @return mixed
     */
    public function update($iId, $aData = array())
    {
        $aFields = array_key_exists('fields', $aData) ? $aData['fields'] : array();
        unset($aData['fields']);

        $this->oDb->trans_begin();
        if (parent::update($iId, $aData)) {

            if ($this->updateFields($iId, $aFields)) {

                $this->oDb->trans_commit();
                return true;

            } else {

                $this->setError('Failed to update fields.');
                $this->oDb->trans_rollback();
                return false;
            }

        } else {

            $this->oDb->trans_rollback();
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @param array $iFormId The ID of the form to which the fields belong
     * @param array $aFields The fields to update or insert
     */
    private function updateFields($iFormId, $aFields)
    {
        /**
         * This array will hold all the IDs we've processed, we'll delete items which
         * aren't created or updated.
         */

        $aProcessedIds = array();

        foreach ($aFields as $aField) {

            $iFieldId = !empty($aField['id']) ? (int) $aField['id'] : null;

            $aFieldData = array(
                'type' => !empty($aField['type']) ? $aField['type'] : 'TEXT',
                'label' => !empty($aField['label']) ? $aField['label'] : '',
                'sub_label' => !empty($aField['sub_label']) ? $aField['sub_label'] : '',
                'placeholder' => !empty($aField['placeholder']) ? $aField['placeholder'] : '',
                'is_required' => !empty($aField['is_required']) ? (bool) $aField['is_required'] : false,
                'default_value' => !empty($aField['default_value']) ? $aField['default_value'] : '',
                'default_value_custom' => !empty($aField['default_value_custom']) ? $aField['default_value_custom'] : '',
                'custom_attributes' => !empty($aField['custom_attributes']) ? $aField['custom_attributes'] : '',
                'order' => !empty($aField['order']) ? (int) $aField['order'] : 0
            );

            $this->oDb->set($aFieldData);

            if (!empty($iFieldId)) {

                $this->oDb->where('id', $iFieldId);
                $sAction = 'update';

            } else {

                $this->oDb->set('form_id', $iFormId);
                $sAction = 'insert';
            }

            if ($this->oDb->{$sAction}($this->tableFields)) {

                if ($sAction === 'insert') {

                    $iFieldId = $this->oDb->insert_id();
                }

                $aProcessedIds[] = $iFieldId;

                if (!empty($aField['options'])) {

                    if (!$this->updateOptions($iFieldId, $aField['options'])) {

                        $this->setError('Failed to update options for form field "' . $aFieldData['label'] . '"');
                        return false;
                    }

                }

            } else {

                $this->setError('Failed to ' . $sAction . ' form field "' . $aFieldData['label'] . '"');
                return false;
            }
        }

        //  Remove untouched fields
        if (!empty($aProcessedIds)) {

            $this->oDb->where_not_in('id', $aProcessedIds);
        }
        $this->oDb->where('form_id', $iFormId);
        if (!$this->oDb->delete($this->tableFields)) {

            $this->setError('Failed to prune unused fields');
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------------

    private function updateOptions($iFieldId, $aOptions)
    {
        /**
         * This array will hold all the IDs we've processed, we'll delete items which
         * aren't created or updated.
         */

        $aProcessedIds = array();

        foreach ($aOptions as $aOption) {

            $iOptionId = !empty($aOption['id']) ? (int) $aOption['id'] : null;

            $aOptionData = array(
                'label' => !empty($aOption['label']) ? $aOption['label'] : '',
                'is_selected' => !empty($aOption['is_selected']) ? (bool) $aOption['is_selected'] : false,
                'is_disabled' => !empty($aOption['is_disabled']) ? (bool) $aOption['is_disabled'] : false,
                'order' => !empty($aOption['order']) ? (int) $aOption['order'] : 0
            );

            $this->oDb->set($aOptionData);

            if (!empty($iOptionId)) {

                $this->oDb->where('id', $iOptionId);
                $sAction = 'update';

            } else {

                $this->oDb->set('form_field_id', $iFieldId);
                $sAction = 'insert';
            }

            if ($this->oDb->{$sAction}($this->tableOptions)) {

                if ($sAction === 'insert') {

                    $iOptionsId = $this->oDb->insert_id();
                }

                $aProcessedIds[] = $iOptionId;

            } else {

                $this->setError('Failed to ' . $sAction . ' form field "' . $aFieldData['label'] . '"');
                return false;
            }
        }

        //  Remove untouched fields
        if (!empty($aProcessedIds)) {

            $this->oDb->where_not_in('id', $aProcessedIds);
        }
        $this->oDb->where('form_field_id', $iFormId);
        if (!$this->oDb->delete($this->tableOptions)) {

            $this->setError('Failed to prune unused options');
            return false;
        }

        return true;
    }
}
