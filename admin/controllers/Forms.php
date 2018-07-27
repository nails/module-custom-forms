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
use Nails\Factory;

class Forms extends BaseAdmin
{
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
        $aPermissions = parent::permissions();

        $aPermissions['browse']           = 'Can browse forms';
        $aPermissions['create']           = 'Can create forms';
        $aPermissions['edit']             = 'Can edit forms';
        $aPermissions['delete']           = 'Can delete forms';
        $aPermissions['responses']        = 'Can view responses';
        $aPermissions['responses_delete'] = 'Can delete responses';

        return $aPermissions;
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

        $oInput     = Factory::service('Input');
        $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Browse Forms';

        // --------------------------------------------------------------------------

        //  Get pagination and search/sort variables
        $sTableAlias = $oFormModel->getTableAlias();
        $iPage       = (int) $oInput->get('page') ? $oInput->get('page') : 0;
        $iPerPage    = (int) $oInput->get('perPage') ? $oInput->get('perPage') : 50;
        $sSortOn     = $oInput->get('sortOn') ? $oInput->get('sortOn') : $sTableAlias . '.label';
        $sSortOrder  = $oInput->get('sortOrder') ? $oInput->get('sortOrder') : 'asc';
        $sKeywords   = $oInput->get('keywords') ? $oInput->get('keywords') : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = [
            $sTableAlias . '.id'       => 'Form ID',
            $sTableAlias . '.label'    => 'Label',
            $sTableAlias . '.modified' => 'Modified Date',
        ];

        // --------------------------------------------------------------------------

        //  Define the $aData variable for the queries
        $aData = [
            'sort'     => [
                [$sSortOn, $sSortOrder],
            ],
            'keywords' => $sKeywords,
            'expand'   => ['responses'],
        ];

        //  Get the items for the page
        $totalRows           = $oFormModel->countAll($aData);
        $this->data['forms'] = $oFormModel->getAll($iPage, $iPerPage, $aData);

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sSortOn, $sSortOrder, $iPerPage, $sKeywords);
        $this->data['pagination'] = Helper::paginationObject($iPage, $iPerPage, $totalRows);

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

        $oInput = Factory::service('Input');
        if ($oInput->post()) {
            if ($this->runFormValidation()) {

                $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');

                if ($oFormModel->create($this->getPostObject())) {

                    $oSession = Factory::service('Session', 'nailsapp/module-auth');
                    $oSession->setFlashData('success', 'Form created successfully.');
                    redirect('admin/forms/forms');

                } else {
                    $this->data['error'] = 'Failed to create form.' . $oFormModel->lastError();
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

        $oInput     = Factory::service('Input');
        $oUri       = Factory::service('Uri');
        $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');

        $iFormId            = (int) $oUri->segment(5);
        $this->data['form'] = $oFormModel->getById(
            $iFormId,
            [
                'expand' => [
                    [
                        'form',
                        [
                            'expand' => [
                                [
                                    'fields',
                                    ['expand' => ['options']],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        if (empty($this->data['form'])) {
            show404();
        }

        if ($oInput->post()) {
            if ($this->runFormValidation()) {
                if ($oFormModel->update($iFormId, $this->getPostObject())) {

                    $oSession = Factory::service('Session', 'nailsapp/module-auth');
                    $oSession->setFlashData('success', 'Form updated successfully.');
                    redirect('admin/forms/forms');

                } else {
                    $this->data['error'] = 'Failed to update form. ' . $oFormModel->lastError();
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
        $oAsset = Factory::service('Asset');
        $oAsset->load('admin.form.edit.min.js', 'nailsapp/module-custom-forms');

        Factory::helper('formbuilder', 'nailsapp/module-form-builder');
        adminLoadFormBuilderAssets('#custom-form-fields');

        $oCaptcha                        = Factory::service('Captcha', 'nailsapp/module-captcha');
        $this->data['bIsCaptchaEnabled'] = $oCaptcha->isEnabled();
    }

    // --------------------------------------------------------------------------

    protected function runFormValidation()
    {
        $oFormValidation = Factory::service('FormValidation');
        $oInput          = Factory::service('Input');

        //  Define the rules
        $aRules = [
            'label'                  => 'required',
            'header'                 => '',
            'footer'                 => '',
            'cta_label'              => '',
            'cta_attributes'         => '',
            'form_attributes'        => '',
            'is_minimal'             => '',
            'has_captcha'            => '',
            'notification_email'     => 'valid_emails',
            'thankyou_email'         => '',
            'thankyou_email_subject' => '',
            'thankyou_email_body'    => '',
            'thankyou_page_title'    => 'required',
            'thankyou_page_body'     => '',
        ];

        foreach ($aRules as $sKey => $sRules) {
            $oFormValidation->set_rules($sKey, '', $sRules);
        }

        $oFormValidation->set_message('required', lang('fv_required'));
        $oFormValidation->set_message('valid_emails', lang('fv_valid_emails'));

        $bValidForm = $oFormValidation->run();

        //  Validate fields
        Factory::helper('formbuilder', 'nailsapp/module-form-builder');
        $bValidFields = adminValidateFormData($oInput->post('fields'));

        return $bValidForm && $bValidFields;
    }

    // --------------------------------------------------------------------------

    protected function getPostObject()
    {
        Factory::helper('formbuilder', 'nailsapp/module-form-builder');
        $oInput  = Factory::service('Input');
        $iFormId = !empty($this->data['form']->form->id) ? $this->data['form']->form->id : null;
        $aData   = [
            'label'                  => $oInput->post('label'),
            'header'                 => $oInput->post('header'),
            'footer'                 => $oInput->post('footer'),
            'cta_label'              => $oInput->post('cta_label'),
            'cta_attributes'         => $oInput->post('cta_attributes'),
            'form_attributes'        => $oInput->post('form_attributes'),
            'is_minimal'             => (bool) $oInput->post('is_minimal'),
            'thankyou_email'         => (bool) $oInput->post('thankyou_email'),
            'thankyou_email_subject' => $oInput->post('thankyou_email_subject'),
            'thankyou_email_body'    => $oInput->post('thankyou_email_body'),
            'thankyou_page_title'    => $oInput->post('thankyou_page_title'),
            'thankyou_page_body'     => $oInput->post('thankyou_page_body'),
            'form'                   => adminNormalizeFormData(
                $iFormId,
                $oInput->post('has_captcha'),
                $oInput->post('fields')
            ),
        ];

        //  Format the emails
        $aEmails = explode(',', $oInput->post('notification_email'));
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

        $oInput     = Factory::service('Input');
        $oUri       = Factory::service('Uri');
        $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');

        $iFormId = (int) $oUri->segment(5);
        $sReturn = $oInput->get('return') ? $oInput->get('return') : 'admin/forms/forms/index';

        if ($oFormModel->delete($iFormId)) {
            $sStatus  = 'success';
            $sMessage = 'Custom form was deleted successfully.';
        } else {
            $sStatus  = 'error';
            $sMessage = 'Custom form failed to delete. ' . $oFormModel->lastError();
        }

        $oSession = Factory::service('Session', 'nailsapp/module-auth');
        $oSession->setFlashData($sStatus, $sMessage);
        redirect($sReturn);
    }

    // --------------------------------------------------------------------------

    public function responses()
    {
        if (!userHasPermission('admin:forms:forms:responses')) {
            unauthorised();
        }

        $oUri       = Factory::service('Uri');
        $oFormModel = Factory::model('Form', 'nailsapp/module-custom-forms');

        $iFormId            = (int) $oUri->segment(5);
        $this->data['form'] = $oFormModel->getById($iFormId, ['expand' => ['responses']]);

        if (empty($this->data['form'])) {
            show404();
        }

        $iResponseId     = (int) $oUri->segment(6);
        $sResponseMethod = $oUri->segment(7) ?: 'view';

        if (empty($iResponseId)) {

            return $this->responsesList();

        } else {

            $oResponseModel = Factory::model('Response', 'nailsapp/module-custom-forms');

            $this->data['response'] = $oResponseModel->getById($iResponseId);

            if (!$this->data['response']) {
                show404();
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
        $oResponseModel = Factory::model('Response', 'nailsapp/module-custom-forms');

        $aData = [
            'where' => [
                ['form_id', $this->data['form']->id],
            ],
        ];

        $this->data['responses'] = $oResponseModel->getAll(null, null, $aData);

        if ($oInput->get('dl')) {
            // FILE
            $aResult = [];

            // header
            $aRow    = ['Created'];
            $oHeader = reset($this->data['responses']);

            foreach ($oHeader->answers as $oColumn) {
                $aRow[] = $oColumn->question;
            }

            $aResult[] = $aRow;

            // answers
            foreach ($this->data['responses'] as $oResponse) {
                $aRow = [$oResponse->created];

                foreach ($oResponse->answers as $oColumn) {
                    $aRow[] = is_array($oColumn->answer) ? implode('|', $oColumn->answer) : $oColumn->answer;
                }

                $aResult[] = $aRow;
            }

            Helper::loadCsv($aResult, $this->data['form']->slug . '.csv');

        } else {
            // PAGE
            Helper::addHeaderButton(
                'admin/forms/forms/responses/' . $this->data['form']->id . '?dl=1',
                'Download as CSV'
            );

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

        $oInput = Factory::service('Input');

        if ($oInput->get('dl')) {
            $oSession = Factory::service('Session', 'nailsapp/module-auth');
            $oSession->setFlashData('warning', '@todo - Download as CSV');
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
        $oSession->setFlashData('warning', '@todo - delete individual responses');
        redirect('admin/forms/forms/responses/' . $this->data['form']->id);
    }
}
