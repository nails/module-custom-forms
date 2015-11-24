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

            $navGroup = Factory::factory('Nav', 'nailsapp/module-admin');
            $navGroup->setLabel('Custom Forms');
            $navGroup->setIcon('fa-list-alt');
            $navGroup->addAction('Browse Forms');
            return $navGroup;
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

        $permissions['browse']    = 'Can browse forms';
        $permissions['create']    = 'Can create forms';
        $permissions['edit']      = 'Can edit forms';
        $permissions['delete']    = 'Can delete forms';
        $permissions['responses'] = 'Can view responses';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        $this->oFormModel     = Factory::model('Form', 'nailsapp/module-custom-forms');
        $this->oResponseModel = Factory::model('Response', 'nailsapp/module-custom-forms');
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
            $tablePrefix . '.id'    => 'Form ID',
            $tablePrefix . '.label' => 'Label'
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'keywords' => $keywords,
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

            $oFormValidation = Factory::service('FormValidation');

            $oFormValidation->set_rules(
                array(
                    array(
                        'field' => 'label',
                        'rules' => 'required'
                    ),
                    array(
                        'field' => 'notification_email',
                        'rules' => 'valid_emails'
                    )
                )
            );

            $oFormValidation->set_message('required', lang('fv_required'));
            $oFormValidation->set_message('valid_emails', lang('fv_valid_emails'));

            if ($oFormValidation->run()) {

                $aCreateData = array();
                $aCreateData['label'] = $this->input->post('label');
                $aCreateData['header'] = $this->input->post('header');
                $aCreateData['footer'] = $this->input->post('footer');
                $aCreateData['cta_label'] = $this->input->post('cta_label');
                $aCreateData['form_attributes'] = $this->input->post('form_attributes');
                $aCreateData['notification_email'] = $this->input->post('notification_email');
                $aCreateData['thankyou_email'] = (bool) $this->input->post('thankyou_email');
                $aCreateData['thankyou_email_subject'] = $this->input->post('thankyou_email_subject');
                $aCreateData['thankyou_email_body'] = $this->input->post('thankyou_email_body');
                $aCreateData['thankyou_page_title'] = $this->input->post('thankyou_page_title');
                $aCreateData['thankyou_page_body'] = $this->input->post('thankyou_page_body');

                //  Build up fields
                $aCreateData['fields'] = array();
                $iFieldOrder = 0;
                $aFields = $this->input->post('fields') ? $this->input->post('fields') : array();

                foreach ($aFields as $aField) {

                    $aTemp = array(
                        'id' => !empty($aField['id']) ? (int) $aField['id'] : null,
                        'type' => !empty($aField['type']) ? $aField['type'] : 'TEXT',
                        'label' => !empty($aField['label']) ? $aField['label'] : '',
                        'sub_label' => !empty($aField['sub_label']) ? $aField['sub_label'] : '',
                        'placeholder' => !empty($aField['placeholder']) ? $aField['placeholder'] : '',
                        'is_required' => !empty($aField['is_required']) ? (bool) $aField['is_required'] : false,
                        'default_value' => !empty($aField['default_value']) ? $aField['default_value'] : '',
                        'default_value_custom' => !empty($aField['default_value_custom']) ? $aField['default_value_custom'] : '',
                        'custom_attributes' => !empty($aField['custom_attributes']) ? $aField['custom_attributes'] : '',
                        'order' => $iFieldOrder,
                        'options' => array()
                    );

                    if (!empty($aField['options'])) {

                        $iOptionOrder = 0;

                        foreach ($aField['options'] as $aOption) {

                            $aTemp['options'][] = array(
                                'id' => !empty($aOption['id']) ? (int) $aOption['id'] : null,
                                'label' => !empty($aOption['label']) ? $aOption['label'] : '',
                                'is_selected' => !empty($aOption['is_selected']) ? $aOption['is_selected'] : false,
                                'is_disabled' => !empty($aOption['is_disabled']) ? $aOption['is_disabled'] : false,
                                'order' => $iOptionOrder
                            );

                            $iOptionOrder++;
                        }
                    }

                    $aCreateData['fields'][] = $aTemp;
                    $iFieldOrder++;
                }

                if ($this->oFormModel->create($aCreateData)) {

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
        $this->asset->load('nails.admin.custom.forms.edit.min.js', 'NAILS');
        $this->asset->load('mustache.js/mustache.js', 'NAILS-BOWER');
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
        $this->data['form'] = $this->oFormModel->getById($iFormId);

        if (empty($this->data['form'])) {
            show_404();
        }

        if ($this->input->post()) {

            $oFormValidation = Factory::service('FormValidation');

            $oFormValidation->set_rules(
                array(
                    array(
                        'field' => 'label',
                        'rules' => 'required'
                    ),
                    array(
                        'field' => 'notification_email',
                        'rules' => 'valid_emails'
                    )
                )
            );

            $oFormValidation->set_message('required', lang('fv_required'));

            if ($oFormValidation->run()) {

                $aUpdateData = array(
                    'label' => $this->input->post('label'),
                    'header' => $this->input->post('header'),
                    'footer' => $this->input->post('footer'),
                    'cta_label' => $this->input->post('cta_label'),
                    'form_attributes' => $this->input->post('form_attributes'),
                    'notification_email' => $this->input->post('notification_email'),
                    'thankyou_email' => (bool) $this->input->post('thankyou_email'),
                    'thankyou_email_subject' => $this->input->post('thankyou_email_subject'),
                    'thankyou_email_body' => $this->input->post('thankyou_email_body'),
                    'thankyou_page_title' => $this->input->post('thankyou_page_title'),
                    'thankyou_page_body' => $this->input->post('thankyou_page_body'),
                    'fields' => array()
                );

                //  Build up fields
                $iFieldOrder = 0;

                foreach ($this->input->post('fields') as $aField) {

                    $aTemp = array(
                        'id' => !empty($aField['id']) ? (int) $aField['id'] : null,
                        'type' => !empty($aField['type']) ? $aField['type'] : 'TEXT',
                        'label' => !empty($aField['label']) ? $aField['label'] : '',
                        'sub_label' => !empty($aField['sub_label']) ? $aField['sub_label'] : '',
                        'placeholder' => !empty($aField['placeholder']) ? $aField['placeholder'] : '',
                        'is_required' => !empty($aField['is_required']) ? (bool) $aField['is_required'] : false,
                        'default_value' => !empty($aField['default_value']) ? $aField['default_value'] : '',
                        'default_value_custom' => !empty($aField['default_value_custom']) ? $aField['default_value_custom'] : '',
                        'custom_attributes' => !empty($aField['custom_attributes']) ? $aField['custom_attributes'] : '',
                        'order' => $iFieldOrder,
                        'options' => array()
                    );

                    if (!empty($aField['options'])) {

                        $iOptionOrder = 0;

                        foreach ($aField['options'] as $aOption) {

                            $aTemp['options'][] = array(
                                'id' => !empty($aOption['id']) ? (int) $aOption['id'] : null,
                                'label' => !empty($aOption['label']) ? $aOption['label'] : '',
                                'is_selected' => !empty($aOption['is_selected']) ? $aOption['is_selected'] : false,
                                'is_disabled' => !empty($aOption['is_disabled']) ? $aOption['is_disabled'] : false,
                                'order' => $iOptionOrder
                            );

                            $iOptionOrder++;
                        }
                    }

                    $aUpdateData['fields'][] = $aTemp;
                    $iFieldOrder++;
                }

                if ($this->oFormModel->update($iFormId, $aUpdateData)) {

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
        $this->asset->load('nails.admin.custom.forms.edit.min.js', 'NAILS');
        $this->asset->load('mustache.js/mustache.js', 'NAILS-BOWER');
        Helper::loadView('edit');
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
        $this->data['form'] = $this->oFormModel->getById($iFormId);

        if (empty($this->data['form'])) {
            show_404();
        }

        $iResponseId = (int) $this->uri->segment(6);

        if (empty($iResponseId)) {

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

                dump('@todo: download as CSV');

            } else {

                $this->data['page']->title = 'Responses for form: ' . $this->data['form']->label;
                Helper::loadView('responses');
            }

        } else {

            $this->data['response'] = $this->oResponseModel->getById($iResponseId);

            if (!$this->data['response']) {
                show_404();
            }

            Helper::addHeaderButton(
                'admin/forms/forms/responses/' . $this->data['form']->id . '/' . $iResponseId . '?dl=1',
                'Download as CSV'
            );

            if ($this->input->get('dl')) {

                dump('@todo: download as CSV');

            } else {

                $this->data['page']->title = 'Responses for form: ' . $this->data['form']->label;
                Helper::loadView('response');
            }
        }
    }
}
