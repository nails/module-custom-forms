<?php

/**
 * Manage Custom forms
 *
 * @package     module-custom-forms
 * @subpackage  Admin
 * @category    AdminController
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Forms;

use Nails\Factory;
use Nails\Admin\Helper;
use Nails\CustomForms\Controller\BaseAdmin;

class Forms extends BaseAdmin
{
    private $oFormModel;
    private $oResponseModel;

    // --------------------------------------------------------------------------

    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:forms:forms:browse')) {

            $oNavGroup = Factory::factory('Nav', 'nailsapp/module-admin');
            $oNavGroup->setLabel('Custom Forms');
            $oNavGroup->setIcon('fa-list-alt');
            $oNavGroup->addAction('Browse Forms');
            return $oNavGroup;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of extra permissions for this controller
     * @return array
     */
    public static function permissions()
    {
        $permissions = parent::permissions();

        $permissions['browse']           = 'Can browse forms';
        $permissions['create']           = 'Can create forms';
        $permissions['edit']             = 'Can edit forms';
        $permissions['delete']           = 'Can delete forms';
        $permissions['responses']        = 'Can view responses';
        $permissions['responses_delete'] = 'Can delete responses';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->oFormModel      = Factory::model('Form', 'nailsapp/module-custom-forms');
        $this->oFormFieldModel = Factory::model('FormField', 'nailsapp/module-custom-forms');
        $this->oResponseModel  = Factory::model('Response', 'nailsapp/module-custom-forms');
    }

    // --------------------------------------------------------------------------

    /**
     * Browse existing form
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:forms:forms:browse')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Browse Forms';

        // --------------------------------------------------------------------------

        //  Get pagination and search/sort variables
        $tablePrefix = $this->oFormModel->getTablePrefix();
        $page        = $this->input->get('page')      ? $this->input->get('page')      : 0;
        $perPage     = $this->input->get('perPage')   ? $this->input->get('perPage')   : 50;
        $sortOn      = $this->input->get('sortOn')    ? $this->input->get('sortOn')    : $tablePrefix . '.label';
        $sortOrder   = $this->input->get('sortOrder') ? $this->input->get('sortOrder') : 'asc';
        $keywords    = $this->input->get('keywords')  ? $this->input->get('keywords')  : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = array(
            $tablePrefix . '.id'       => 'Form ID',
            $tablePrefix . '.label'    => 'Label',
            $tablePrefix . '.modified' => 'Modified Date'
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'keywords'         => $keywords,
            'includeResponses' => true
        );

        //  Get the items for the page
        $totalRows           = $this->oFormModel->countAll($data);
        $this->data['forms'] = $this->oFormModel->getAll($page, $perPage, $data);

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sortOn, $sortOrder, $perPage, $keywords);
        $this->data['pagination'] = Helper::paginationObject($page, $perPage, $totalRows);

        //  Add a header button
        if (userHasPermission('admin:forms:forms:create')) {

             Helper::addHeaderButton('admin/forms/forms/create', 'Create Form');
        }

        // --------------------------------------------------------------------------

        Helper::loadView('index');
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new Form
     * @return void
     */
    public function create()
    {
        if (!userHasPermission('admin:forms:forms:create')) {
            unauthorised();
        }

        if ($this->input->post()) {
            if ($this->runFormValidation()) {
                if ($this->oFormModel->create($this->getPostObject())) {

                    $this->session->set_flashdata('success', 'Form created successfully.');
                    redirect('admin/forms/forms');

                } else {

                    $this->data['error'] = 'Failed to create form.' . $this->oFormModel->lastError();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        $this->data['page']->title = 'Create Form';
        $this->loadViewData();
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Edit an existing Form
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission('admin:forms:forms:edit')) {
            unauthorised();
        }

        $iFormId = (int) $this->uri->segment(5);
        $this->data['form'] = $this->oFormModel->getById($iFormId, array('includeFields' => true));

        if (empty($this->data['form'])) {
            show_404();
        }

        if ($this->input->post()) {
            if ($this->runFormValidation()) {
                if ($this->oFormModel->update($iFormId, $this->getPostObject())) {

                    $this->session->set_flashdata('success', 'Form updated successfully.');
                    redirect('admin/forms/forms');

                } else {

                    $this->data['error'] = 'Failed to update form.' . $this->oFormModel->lastError();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        $this->data['page']->title = 'Edit Form';
        $this->loadViewData();
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    protected function loadViewData()
    {
        $this->data['aFieldTypes']            = $this->oFormFieldModel->getTypesFlat();
        $this->data['aFieldDefaultValues']    = $this->oFormFieldModel->getDefaultValuesFlat();

        $oAsset = Factory::service('Asset');
        $oAsset->load('admin.form.edit.min.js', 'nailsapp/module-custom-forms');
        $oAsset->inline(
            '
                var _admin_custom_forms_edit = new _ADMIN_CUSTOM_FORMS_EDIT(
                    ' . json_encode($this->oFormFieldModel->getTypesWithOptions()) . ',
                    ' . json_encode($this->oFormFieldModel->getTypesWithDefaultValue()) . '
                );
            ',
            'JS'
        );
    }

    // --------------------------------------------------------------------------

    protected function runFormValidation()
    {
        $oFormValidation = Factory::service('FormValidation');

        //  Define the rules
        $aRules = array(
            'label'                  => 'xss_clean|required',
            'header'                 => '',
            'footer'                 => '',
            'cta_label'              => 'xss_clean',
            'cta_attributes'         => 'xss_clean',
            'form_attributes'        => 'xss_clean',
            'has_captcha'            => '',
            'fields'                 => 'required',
            'notification_email'     => 'valid_emails',
            'thankyou_email'         => '',
            'thankyou_email_subject' => 'xss_clean',
            'thankyou_email_body'    => 'xss_clean',
            'thankyou_page_title'    => 'xss_clean|required',
            'thankyou_page_body'     => '',
        );

        foreach ($aRules as $sKey => $sRules) {
            $oFormValidation->set_rules($sKey, '', $sRules);
        }

        $oFormValidation->set_message('required', lang('fv_required'));
        $oFormValidation->set_message('valid_emails', lang('fv_valid_emails'));

        return $oFormValidation->run();
    }

    // --------------------------------------------------------------------------

    protected function getPostObject()
    {
        $aData = array(
            'label'                  => $this->input->post('label'),
            'header'                 => $this->input->post('header'),
            'footer'                 => $this->input->post('footer'),
            'cta_label'              => $this->input->post('cta_label'),
            'cta_attributes'         => $this->input->post('cta_attributes'),
            'form_attributes'        => $this->input->post('form_attributes'),
            'thankyou_email'         => (bool) $this->input->post('thankyou_email'),
            'thankyou_email_subject' => $this->input->post('thankyou_email_subject'),
            'thankyou_email_body'    => $this->input->post('thankyou_email_body'),
            'thankyou_page_title'    => $this->input->post('thankyou_page_title'),
            'thankyou_page_body'     => $this->input->post('thankyou_page_body'),
            'fields'                 => array()
        );

        //  Build up fields
        $iFieldOrder = 0;
        $aFields     = $this->input->post('fields') ?: array();

        foreach ($aFields as $aField) {

            $aTemp = array(
                'id'                   => !empty($aField['id']) ? (int) $aField['id'] : null,
                'type'                 => !empty($aField['type']) ? $aField['type'] : 'TEXT',
                'label'                => !empty($aField['label']) ? $aField['label'] : '',
                'sub_label'            => !empty($aField['sub_label']) ? $aField['sub_label'] : '',
                'placeholder'          => !empty($aField['placeholder']) ? $aField['placeholder'] : '',
                'is_required'          => !empty($aField['is_required']) ? (bool) $aField['is_required'] : false,
                'default_value'        => !empty($aField['default_value']) ? $aField['default_value'] : '',
                'default_value_custom' => !empty($aField['default_value_custom']) ? $aField['default_value_custom'] : '',
                'custom_attributes'    => !empty($aField['custom_attributes']) ? $aField['custom_attributes'] : '',
                'order'                => $iFieldOrder,
                'options'              => array()
            );

            if (!empty($aField['options'])) {

                $iOptionOrder = 0;

                foreach ($aField['options'] as $aOption) {

                    $aTemp['options'][] = array(
                        'id'          => !empty($aOption['id']) ? (int) $aOption['id'] : null,
                        'label'       => !empty($aOption['label']) ? $aOption['label'] : '',
                        'is_selected' => !empty($aOption['is_selected']) ? $aOption['is_selected'] : false,
                        'is_disabled' => !empty($aOption['is_disabled']) ? $aOption['is_disabled'] : false,
                        'order'       => $iOptionOrder
                    );

                    $iOptionOrder++;
                }
            }

            $aData['fields'][] = $aTemp;
            $iFieldOrder++;
        }

        //  Format the emails
        $aEmails = explode(',', $this->input->post('notification_email'));
        $aEmails = array_map('trim', $aEmails);
        $aEmails = array_unique($aEmails);
        $aEmails = array_filter($aEmails);

        $aData['notification_email'] = json_encode($aEmails);

        return $aData;
    }

    // --------------------------------------------------------------------------

    /**
     * Delete an existing form
     * @return void
     */
    public function delete()
    {
        if (!userHasPermission('admin:forms:forms:delete')) {

            unauthorised();
        }

        $iFormId = (int) $this->uri->segment(5);
        $sReturn = $this->input->get('return') ? $this->input->get('return') : 'admin/forms/forms/index';

        if ($this->oFormModel->delete($iFormId)) {

            $sStatus  = 'success';
            $sMessage = 'Custom form was deleted successfully.';

        } else {

            $sStatus  = 'error';
            $sMessage = 'Custom form failed to delete. ' . $this->oFormModel->lastError();
        }

        $this->session->set_flashdata($sStatus, $sMessage);
        redirect($sReturn);
    }

    // --------------------------------------------------------------------------

    public function responses()
    {
        if (!userHasPermission('admin:forms:forms:responses')) {

            unauthorised();
        }

        $iFormId = (int) $this->uri->segment(5);
        $this->data['form'] = $this->oFormModel->getById($iFormId, array('includeResponses' => true));

        if (empty($this->data['form'])) {
            show_404();
        }

        $iResponseId     = (int) $this->uri->segment(6);
        $sResponseMethod = $this->uri->segment(7) ?: 'view';

        if (empty($iResponseId)) {

            return $this->responsesList();

        } else {

            $this->data['response'] = $this->oResponseModel->getById($iResponseId);

            if (!$this->data['response']) {
                show_404();
            }

            switch ($sResponseMethod) {

                case 'delete':
                    return $this->responseDelete();
                    break;

                default:
                case 'view':
                    return $this->responseView();
                    break;
            }
        }
    }

    // --------------------------------------------------------------------------

    protected function responsesList()
    {
        $aData = array(
          'where' => array(
              array('form_id', $this->data['form']->id)
          )
        );
        $this->data['responses'] = $this->oResponseModel->getAll(null, null, $aData);

        Helper::addHeaderButton(
            'admin/forms/forms/responses/' . $this->data['form']->id . '?dl=1',
            'Download as CSV'
        );

        if ($this->input->get('dl')) {

            $oSession = Factory::service('Session', 'nailsapp/module-auth');
            $oSession->set_flashdata('warning', '@todo - Download as CSV');
            redirect('admin/forms/forms/responses/' . $this->data['form']->id);

        } else {

            $this->data['page']->title = 'Responses for form: ' . $this->data['form']->label;
            Helper::loadView('responses');
        }
    }

    // --------------------------------------------------------------------------

    protected function responseView()
    {
        Helper::addHeaderButton(
            'admin/forms/forms/responses/' . $this->data['form']->id . '/' . $this->data['response']->id . '?dl=1',
            'Download as CSV'
        );

        // --------------------------------------------------------------------------

        if ($this->input->get('dl')) {

            $oSession = Factory::service('Session', 'nailsapp/module-auth');
            $oSession->set_flashdata('warning', '@todo - Download as CSV');
            redirect('admin/forms/forms/responses/' . $this->data['form']->id . '/' . $this->data['response']->id);

        } else {

            $this->data['page']->title = 'Responses for form: ' . $this->data['form']->label;
            Helper::loadView('response');
        }
    }

    // --------------------------------------------------------------------------

    protected function responseDelete()
    {
        $oSession = Factory::service('Session', 'nailsapp/module-auth');
        $oSession->set_flashdata('warning', '@todo - delete individual responses');
        redirect('admin/forms/forms/responses/' . $this->data['form']->id);
    }
}
