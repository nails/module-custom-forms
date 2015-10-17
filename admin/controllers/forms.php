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

use Nails\Admin\Helper;
use Nails\CustomForms\Controller\BaseAdmin;

class Forms extends BaseAdmin
{
    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:forms:forms:browse')) {

            $navGroup = new \Nails\Admin\Nav('Custom Forms', 'fa-list-alt');
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
        $this->load->model('forms/custom_form_model');
        $this->load->model('forms/custom_form_response_model');
    }

    // --------------------------------------------------------------------------

    /**
     * Browse existing CDN Buckets
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
        $tablePrefix = $this->custom_form_model->getTablePrefix();
        $page      = $this->input->get('page')      ? $this->input->get('page')      : 0;
        $perPage   = $this->input->get('perPage')   ? $this->input->get('perPage')   : 50;
        $sortOn    = $this->input->get('sortOn')    ? $this->input->get('sortOn')    : $tablePrefix . '.label';
        $sortOrder = $this->input->get('sortOrder') ? $this->input->get('sortOrder') : 'asc';
        $keywords  = $this->input->get('keywords')  ? $this->input->get('keywords')  : '';

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
        $totalRows           = $this->custom_form_model->count_all($data);
        $this->data['forms'] = $this->custom_form_model->get_all($page, $perPage, $data);

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
     * Create a new CDN Bucket
     * @return void
     */
    public function create()
    {
        if (!userHasPermission('admin:forms:forms:create')) {

            unauthorised();
        }

        if ($this->input->post()) {

            $this->load->library('form_validation');

            $this->form_validation->set_rules(
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

            $this->form_validation->set_message('required', lang('fv_required'));
            $this->form_validation->set_message('valid_emails', lang('fv_valid_emails'));

            if ($this->form_validation->run()) {

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

                if ($this->custom_form_model->create($aCreateData)) {

                    $this->session->set_flashdata('success', 'Form created successfully.');
                    redirect('admin/forms/forms');

                } else {

                    $this->data['error'] = 'Failed to create form.' . $this->custom_form_model->last_error();
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
     * Edit an existing CDN Bucket
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission('admin:forms:forms:edit')) {

            unauthorised();
        }

        $iFormId = (int) $this->uri->segment(5);
        $this->data['form'] = $this->custom_form_model->get_by_id($iFormId);

        if (empty($this->data['form'])) {
            show_404();
        }

        if ($this->input->post()) {

            $this->load->library('form_validation');

            $this->form_validation->set_rules(
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

            $this->form_validation->set_message('required', lang('fv_required'));

            if ($this->form_validation->run()) {

                $aUpdateData = array();
                $aUpdateData['label'] = $this->input->post('label');
                $aUpdateData['header'] = $this->input->post('header');
                $aUpdateData['footer'] = $this->input->post('footer');
                $aUpdateData['cta_label'] = $this->input->post('cta_label');
                $aUpdateData['form_attributes'] = $this->input->post('form_attributes');
                $aUpdateData['notification_email'] = $this->input->post('notification_email');
                $aUpdateData['thankyou_email'] = (bool) $this->input->post('thankyou_email');
                $aUpdateData['thankyou_email_subject'] = $this->input->post('thankyou_email_subject');
                $aUpdateData['thankyou_email_body'] = $this->input->post('thankyou_email_body');
                $aUpdateData['thankyou_page_title'] = $this->input->post('thankyou_page_title');
                $aUpdateData['thankyou_page_body'] = $this->input->post('thankyou_page_body');

                //  Build up fields
                $aUpdateData['fields'] = array();
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

                if ($this->custom_form_model->update($iFormId, $aUpdateData)) {

                    $this->session->set_flashdata('success', 'Form updated successfully.');
                    redirect('admin/forms/forms');

                } else {

                    $this->data['error'] = 'Failed to update form.' . $this->custom_form_model->last_error();
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

        if ($this->custom_form_model->delete($iFormId)) {

            $sStatus  = 'success';
            $sMessage = 'Custom form was deleted successfully.';

        } else {

            $sStatus  = 'error';
            $sMessage = 'Custom form failed to delete. ' . $this->custom_form_model->last_error();
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
        $this->data['form'] = $this->custom_form_model->get_by_id($iFormId);

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
            $this->data['responses'] = $this->custom_form_response_model->get_all(null, null, $aData);

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

            $this->data['response'] = $this->custom_form_response_model->get_by_id($iResponseId);

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
